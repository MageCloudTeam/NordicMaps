<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Calculator;

use Klarna\Orderlines\Model\Container\DataHolder;
use Magento\Customer\Model\Address\AddressModelInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Base\Helper\DataConverter;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\Calculation;

/**
 * This class calculate metrics for the gift wrap
 *
 * @api
 */
class GiftWrap
{
    /**
     * Gift wrapping tax class
     */
    public const XML_PATH_TAX_CLASS_GW = 'tax/classes/wrapping_tax_class';

    /** @var KlarnaConfig $klarnaConfig */
    private $klarnaConfig;

    /** @var DataConverter $dataConverterHelper */
    private $dataConverterHelper;
    /**
     * @var Calculation
     */
    private $calculator;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param KlarnaConfig $klarnaConfig
     * @param DataConverter $dataConverter
     * @param Calculation $calculator
     * @param ScopeConfigInterface $scopeConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaConfig $klarnaConfig,
        DataConverter $dataConverter,
        Calculation $calculator,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->klarnaConfig = $klarnaConfig;
        $this->dataConverterHelper = $dataConverter;
        $this->calculator = $calculator;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Processing the item
     *
     * @param array          $itemToProcess
     * @param int            $itemQty
     * @param StoreInterface $store
     * @param string         $itemType
     * @return array
     * @throws \Klarna\Base\Exception
     */
    public function getProcessedItem(array $itemToProcess, $itemQty, StoreInterface $store, $itemType)
    {
        $item = $this->getBaseResultItem($itemToProcess, $itemQty, $itemType);

        $tax = $this->getTaxForProcessedItem($itemToProcess);
        $unitPrice = $itemToProcess['gift_wrap_price'] + $tax['amount'];

        $item['tax_rate'] = $this->dataConverterHelper->toApiFloat($tax['rate']);
        $item['total_tax_amount'] = $this->dataConverterHelper->toApiFloat($tax['amount'] * $itemQty);
        $item['unit_price'] = $this->dataConverterHelper->toApiFloat($unitPrice);
        $item['total_amount'] = $this->dataConverterHelper->toApiFloat($unitPrice * $itemQty);

        if ($this->klarnaConfig->isSeparateTaxLine($store)) {
            $item['tax_rate'] = 0;
            $item['total_tax_amount'] = 0;
            $item['unit_price'] = $this->dataConverterHelper->toApiFloat($itemToProcess['gift_wrap_base_price']);
            $item['total_amount'] = $this->dataConverterHelper->toApiFloat(
                $itemToProcess['gift_wrap_base_price'] * $itemQty
            );
        }

        return $item;
    }

    /**
     * Getting back the tax rate and amount for the item which will be processed
     *
     * @param array $itemToProcess
     * @return array
     */
    private function getTaxForProcessedItem(array $itemToProcess)
    {
        $taxRate = $this->getCalculatedTaxRate($itemToProcess);

        $taxAmount = $itemToProcess['gift_wrap_base_tax_amount'];
        /** @noinspection TypeUnsafeComparisonInspection */
        if ($taxAmount === 0) {
            $taxRate = 0;
        }

        return [
            'rate' => $taxRate,
            'amount' => $taxAmount
        ];
    }

    /**
     * Getting back the calculated tax rate of the item
     *
     * @param array $itemToProcess
     * @return float
     */
    private function getCalculatedTaxRate(array $itemToProcess): float
    {
        if ($itemToProcess['gift_wrap_price'] === 0) {
            return 0;
        }
        if ($itemToProcess['tax_percent'] > 0) {
            return $itemToProcess['tax_percent'];
        }
        if ($itemToProcess['gift_wrap_base_tax_amount'] === 0) {
            return 0;
        }
        return 0;
    }

    /**
     * Getting back the base result item
     *
     * @param array $itemToProcess
     * @param int $itemQty
     * @param string $itemType
     * @return array
     */
    private function getBaseResultItem(array $itemToProcess, $itemQty, $itemType)
    {
        return [
            'type'          => $itemType,
            'reference'     => substr(sprintf('%s - Gift Wrapping', $itemToProcess['sku']), 0, 64),
            'name'          => (string)__('Gift Wrapping'),
            'quantity'      => $itemQty,
            'discount_rate' => 0,
        ];
    }

    /**
     * Getting back the item
     *
     * @param DataHolder $dataHolder
     * @param string $itemType
     * @return array|null
     * @throws \Klarna\Base\Exception
     */
    public function getItem(DataHolder $dataHolder, $itemType)
    {
        if ($dataHolder->getGiftWrapId()) {
            $store = $dataHolder->getStore();
            $taxRate = $this->getGiftWrappingTaxRate(
                $dataHolder->getBillingAddress(),
                $dataHolder->getShippingAddress(),
                $store
            );

            $totalAmount = $dataHolder->getGiftWrapBasePrice() * ((100 + $taxRate) / 100);
            $taxAmount = $totalAmount * ($taxRate / (100 + $taxRate));

            if ($this->klarnaConfig->isSeparateTaxLine($store)) {
                $taxRate = 0;
                $taxAmount = 0;
                $totalAmount = $dataHolder->getGiftWrapBasePrice();
            }
            $item = [
                'type'             => $itemType,
                'reference'        => $dataHolder->getGiftWrapId(),
                'name'             => 'Gift Wrapping',
                'quantity'         => 1,
                'unit_price'       => $this->dataConverterHelper->toApiFloat($totalAmount),
                'tax_rate'         => $this->dataConverterHelper->toApiFloat($taxRate),
                'total_amount'     => $this->dataConverterHelper->toApiFloat($totalAmount),
                'total_tax_amount' => $this->dataConverterHelper->toApiFloat($taxAmount),
            ];

            return $item;
        }
        return null;
    }

    /**
     * Returns gift wrap tax rate
     *
     * @param AddressModelInterface $billingAddress
     * @param AddressModelInterface $shippingAddress
     * @param StoreInterface        $store
     * @return float
     */
    private function getGiftWrappingTaxRate(
        AddressModelInterface $billingAddress,
        AddressModelInterface $shippingAddress,
        StoreInterface $store
    ): float {
        $request   = $this->calculator->getRateRequest(
            $billingAddress,
            $shippingAddress,
            null,
            $store
        );
        $taxRateId = $this->scopeConfig->getValue(
            self::XML_PATH_TAX_CLASS_GW,
            ScopeInterface::SCOPE_STORES,
            $store
        );

        return $this->calculator->getRate($request->setProductClassId($taxRateId));
    }

    /**
     * Calculate missing gift wrapping tax
     *
     * @param DataObject    $checkout
     * @param CartInterface $quote
     *
     * @return float|int
     */
    public function getAdditionalGwTax(DataObject $checkout, CartInterface $quote)
    {
        $klarnaTotal = (int)($checkout->getOrderAmount() ?: $checkout->getData('cart/total_price_including_tax'));
        $quoteTotal  = (int)$this->dataConverterHelper->toApiFloat($quote->getGrandTotal());

        if ($klarnaTotal > $quoteTotal) {
            $store   = $quote->getStore();
            $taxRate = $this->getGiftWrappingTaxRate(
                $quote->getBillingAddress(),
                $quote->getShippingAddress(),
                $store
            );

            if ($taxRate > 0
                && $quote->getGwId()
                && $quote->getGwBasePrice() > 0
                && $quote->getGwBaseTaxAmount() === 0) {
                $gwTotalAmount = $quote->getGwBasePrice() * ((100 + $taxRate) / 100);
                $taxAmount     = $gwTotalAmount * ($taxRate / (100 + $taxRate));
                $taxAmount     = (int)$this->dataConverterHelper->toApiFloat($taxAmount);
                //additional validation to ensure only gift wrapping tax is missing from quote total
                if ($klarnaTotal === ($quoteTotal + $taxAmount)) {
                    return $taxAmount;
                }
            }
        }
        return 0;
    }
}
