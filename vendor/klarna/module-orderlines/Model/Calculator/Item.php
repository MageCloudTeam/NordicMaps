<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Calculator;

use Klarna\Base\Exception;
use Klarna\Base\Helper\DataConverter;
use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Orderlines\Model\Items\Items;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Tax\Model\Config;
use Magento\Downloadable\Model\Product\Type as DownloadableType;

/**
 * This class calculates metrics for the cart items
 *
 * @api
 */
class Item
{
    /**
     * @var DataConverter $helper
     */
    private $helper;
    /**
     * @var KlarnaConfig $klarnaConfig
     */
    private $klarnaConfig;
    /**
     * @var Config
     */
    private Config $taxConfig;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param DataConverter   $helper
     * @param KlarnaConfig    $klarnaConfig
     * @param Config          $taxConfig
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        DataConverter $helper,
        KlarnaConfig  $klarnaConfig,
        Config $taxConfig,
        LoggerInterface $logger = null
    ) {
        $this->helper       = $helper;
        $this->klarnaConfig = $klarnaConfig;
        $this->taxConfig    = $taxConfig;
        $this->logger       = $logger ?: ObjectManager::getInstance()->get(
            LoggerInterface::class
        );
    }

    /**
     * Processing the item and getting back the result of it
     *
     * @param StoreInterface $store
     * @param array $item
     * @return array
     * @throws Exception
     */
    public function getProcessedItem(StoreInterface $store, array $item): array
    {
        $itemResult = $this->getBaseResultItem($item);

        $itemResult['type'] = Items::ITEM_TYPE_PHYSICAL;
        if (in_array($item['product_type'], [Type::TYPE_VIRTUAL, DownloadableType::TYPE_DOWNLOADABLE])) {
            $itemResult['type'] = Items::ITEM_TYPE_VIRTUAL;
        }

        if (filter_var($item['qty'], FILTER_VALIDATE_INT) === false) {
            $this->logger->warning(
                'Qty is a float but it needs to be an int type for the product: ' . $item['sku'] .
                'Value: ' . $item['qty']
            );
        }
        if (filter_var($item['qtyMultiplier'], FILTER_VALIDATE_INT) === false) {
            $this->logger->warning(
                'Qty multiplier is a float but it needs to be an int type for the product: ' . $item['sku'] .
                'Value: ' . $item['qtyMultiplier']
            );
        }

        $itemResult['quantity']   = (int) ($item['qty'] * $item['qtyMultiplier']);
        $itemResult['unit_price'] = $this->helper->toApiFloat($item['base_price'])
            ?: $this->helper->toApiFloat($item['base_original_price']);

        $itemResult['total_amount'] = $this->helper->toApiFloat(
            $item['base_row_total'] - $item['base_discount_amount']
        );
        $itemResult['total_discount_amount'] = $this->helper->toApiFloat($item['base_discount_amount']);

        if (!$this->klarnaConfig->isSeparateTaxLine($store)) {
            $itemResult = $this->applyTaxOnItem($itemResult, $item);
        }

        return $itemResult;
    }

    /**
     * Getting back the base result item
     *
     * @param array $item
     * @return array
     */
    private function getBaseResultItem(array $item): array
    {
        $result = [
            'reference'             => substr($item['sku'], 0, 64),
            'name'                  => $item['name'],
            'discount_rate'         => 0,
            'tax_rate'              => 0,
            'total_tax_amount'      => 0,
            'total_discount_amount' => 0
        ];

        if (isset($item['product_url'])) {
            $result['product_url'] = $item['product_url'];
        }

        if (isset($item['image_url'])) {
            $result['image_url'] = $item['image_url'];
        }

        return $result;
    }

    /**
     * Applying the tax on the price and totals values of the item
     *
     * @param array $itemResult
     * @param array $item
     * @return array
     */
    private function applyTaxOnItem(array $itemResult, array $item): array
    {
        $itemResult['tax_rate']         = $this->helper->toApiFloat($item['tax_percent']);
        $exlTaxToInclTax = 1 + $itemResult['tax_rate'] / 10000;
        $itemResult['unit_price']       = $this->helper->toApiFloat($item['base_price_incl_tax'])
            ?: $this->helper->toApiFloat($item['base_row_total_incl_tax']);
        $itemResult['total_amount']     = $this->helper->toApiFloat(
            $item['base_row_total_incl_tax'] - $item['base_discount_amount']
        );

        if (!$this->taxConfig->priceIncludesTax($item['store'])) {
            $itemResult['unit_price']   = $this->helper->toApiFloat(
                $item['base_price'] + ($item['base_tax_amount'] / $item['qty'])
            );
            $itemResult['total_amount'] = $this->helper->toApiFloat(
                $item['base_row_total'] - $item['base_discount_amount'] + $item['base_tax_amount']
            );
        }

        if (!$this->taxConfig->applyTaxAfterDiscount($item['store'])) {
            $itemResult['total_tax_amount'] = round(
                $itemResult['total_amount'] -
                (($itemResult['total_amount'] * 10000) / (10000 + $itemResult['tax_rate']))
            );
        }
        $itemResult['total_tax_amount'] = abs(round($itemResult['total_amount'] - $itemResult['total_amount'] * 10000 / (10000 + $itemResult['tax_rate'])));
        return $itemResult;
    }

    /**
     * Checking if the item can be processed
     *
     * @param array $parentItem
     * @param array $item
     * @return bool
     */
    public function isItemUnprocessable(array $parentItem, array $item)
    {
        // Skip if bundle product with a dynamic price type
        if (Type::TYPE_BUNDLE === $item['product_type']
            && (string) Price::PRICE_TYPE_DYNAMIC === $item['product']->getPriceType()
        ) {
            return true;
        }

        if (empty($parentItem)) {
            return false;
        }

        // Skip if child product of a non bundle parent
        if (Type::TYPE_BUNDLE != $parentItem['product_type']) {
            return true;
        }

        // Skip if non bundle product or if bundled product with a fixed price type
        if (Type::TYPE_BUNDLE != $parentItem['product_type']
            || (string) Price::PRICE_TYPE_FIXED === $parentItem['product']->getPriceType()
        ) {
            return true;
        }

        return false;
    }
}
