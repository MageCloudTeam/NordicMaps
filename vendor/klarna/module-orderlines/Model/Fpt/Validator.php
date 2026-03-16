<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Fpt;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Klarna\Orderlines\Model\ProductTypeChecker;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @internal
 */
class Validator
{
    /**
     * @var ProductTypeChecker
     */
    private ProductTypeChecker $productTypeChecker;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ProductTypeChecker $productTypeChecker
     * @param ScopeConfigInterface $scopeConfig
     * @codeCoverageIgnore
     */
    public function __construct(ProductTypeChecker $productTypeChecker, ScopeConfigInterface $scopeConfig)
    {
        $this->productTypeChecker = $productTypeChecker;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Checking if fpt is usable
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isFptUsable(StoreInterface $store)
    {
        return $this->isFptEnabled($store) && $this->getDisplayInSubtotalFpt($store);
    }

    /**
     * Determine if FPT (Fixed Product Tax) is set to be included in the subtotal
     *
     * @param StoreInterface $store
     * @return string
     */
    private function getDisplayInSubtotalFpt(StoreInterface $store): string
    {
        return $this->scopeConfig->getValue('tax/weee/include_in_subtotal', ScopeInterface::SCOPE_STORES, $store);
    }

    /**
     * Checking if Fixed Product Taxes are enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    private function isFptEnabled(StoreInterface $store): bool
    {
        return $this->scopeConfig->isSetFlag('tax/weee/enable', ScopeInterface::SCOPE_STORES, $store);
    }

    /**
     * Returns true if its a valid order item
     *
     * @param ExtensibleDataInterface $data
     * @param ExtensibleDataInterface $item
     * @return bool
     */
    public function isValidOrderItem(ExtensibleDataInterface $data, ExtensibleDataInterface $item): bool
    {
        if (!$item instanceof InvoiceItemInterface && !$item instanceof CreditmemoItemInterface) {
            return false;
        }

        $orderItem = $item->getOrderItem();
        $parentItem = null;
        if ($orderItem->getParentItem()) {
            $parentItem = $data->getItemById($orderItem->getParentItemId());
        }
        if (!$parentItem) {
            return false;
        }

        if ($this->productTypeChecker->isNonBundleProduct($parentItem)) {
            return false;
        }
        if ($this->productTypeChecker->isBundledProductWithFixedPriceType($parentItem)) {
            return false;
        }
        if ($this->productTypeChecker->isBundledProductWithDynamicPriceType($orderItem)) {
            return false;
        }
        if ($this->productTypeChecker->isBundledProductWithDynamicPriceType($parentItem)) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if its a valid quote item
     *
     * @param ExtensibleDataInterface $data
     * @param ExtensibleDataInterface $item
     * @return bool
     */
    public function isValidQuoteItem(ExtensibleDataInterface $data, ExtensibleDataInterface $item): bool
    {
        if ($item instanceof CartItemInterface) {
            if ($this->productTypeChecker->isBundledProductWithDynamicPriceType($item)) {
                return false;
            }

            $parentItem = $data->getItemById($item->getParentItemId());
            if ($parentItem !== null && null !== $item->getParentItemId()) {
                if ($this->productTypeChecker->isNonBundleProduct($parentItem) ||
                    $this->productTypeChecker->isFixedPriceType($item)) {
                    return false;
                }
            }
        }

        return true;
    }
}
