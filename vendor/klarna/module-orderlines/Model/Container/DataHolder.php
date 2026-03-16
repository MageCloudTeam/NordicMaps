<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Container;

use Magento\Store\Api\Data\StoreInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @internal
 */
class DataHolder
{
    /** @var mixed $shippingAddress */
    private $shippingAddress;

    /** @var mixed $billingAddress */
    private $billingAddress;

    /** @var StoreInterface $store */
    private $store;

    /** @var array $items */
    private $items;

    /**
     * Getting back the flat list of items
     *
     * @var mixed $items
     */
    private $flatItems;

    /** @var float $discountAmount */
    private $discountAmount;

    /** @var string $couponCode */
    private $couponCode;

    /** @var string $discountDescription */
    private $discountDescription;

    /** @var float $baseSubtotalWithDiscount */
    private $baseSubtotalWithDiscount;

    /** @var float $baseSubtotal */
    private $baseSubtotal;

    /** @var int $customerTaxClassId */
    private $customerTaxClassId;

    /** @var array $totals */
    private $totals;

    /** @var float $usedGiftCardAmount */
    private $usedGiftCardAmount;

    /** @var int $giftWrapId */
    private $giftWrapId;

    /** @var float $giftWrapBasePrice */
    private $giftWrapBasePrice;

    /** @var float $baseShippingInclTax */
    private $baseShippingInclTax;

    /** @var float $shippingAmount */
    private $shippingAmount;

    /** @var float $shippingHiddenTaxAmount */
    private $shippingHiddenTaxAmount;

    /** @var bool $virtual */
    private $virtual;

    /** @var array $fptTax */
    private $fptTax;

    /** @var float $totalTax */
    private $totalTax;

    /** @var float $usedCustomerBalanceAmount */
    private $usedCustomerBalanceAmount;

    /**
     * Setting the used customer balance amount
     *
     * @param float $amount
     * @return $this
     */
    public function setUsedCustomerBalanceAmount($amount)
    {
        $this->usedCustomerBalanceAmount = $amount;
        return $this;
    }

    /**
     * Getting back the used customer balance amount
     *
     * @return float
     */
    public function getUsedCustomerBalanceAmount()
    {
        return $this->usedCustomerBalanceAmount;
    }

    /**
     * Setting the total tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTotalTax($tax)
    {
        $this->totalTax = $tax;
        return $this;
    }

    /**
     * Getting back the total tax
     *
     * @return float
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * Setting the fpt tax
     *
     * @param array $tax
     * @return $this
     */
    public function setFptTax(array $tax)
    {
        $this->fptTax = $tax;
        return $this;
    }

    /**
     * Getting back the fpt tax
     *
     * @return array
     */
    public function getFptTax()
    {
        return $this->fptTax;
    }

    /**
     * Setting the virtual flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setVirtualFlag($flag)
    {
        $this->virtual = $flag;
        return $this;
    }

    /**
     * Checking if we have virtual products
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->virtual;
    }

    /**
     * Setting the shipping hidden tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingHiddenTaxAmount($amount)
    {
        $this->shippingHiddenTaxAmount = $amount;
        return $this;
    }

    /**
     * Getting back the shipping hidden tax amount
     *
     * @return float
     */
    public function getShippingHiddenTaxAmount()
    {
        return (int) $this->shippingHiddenTaxAmount;
    }

    /**
     * Setting the shipping amount
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingAmount($amount)
    {
        $this->shippingAmount = $amount;
        return $this;
    }

    /**
     * Getting back the shipping amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return (int) $this->shippingAmount;
    }

    /**
     * Setting the base shipping amount inclusive tax
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseShippingInclTax($amount)
    {
        $this->baseShippingInclTax = $amount;
        return $this;
    }

    /**
     * Getting back the base shipping amount inclusive tax
     *
     * @return float
     */
    public function getBaseShippingInclTax()
    {
        return (int) $this->baseShippingInclTax;
    }

    /**
     * Setting the gift wrap base price
     *
     * @param float $price
     * @return $this
     */
    public function setGiftWrapBasePrice($price)
    {
        $this->giftWrapBasePrice = $price;
        return $this;
    }

    /**
     * Getting back the gift wrap base price
     *
     * @return float
     */
    public function getGiftWrapBasePrice()
    {
        return $this->giftWrapBasePrice;
    }

    /**
     * Setting the gift wrap id
     *
     * @param int $id
     * @return $this
     */
    public function setGiftWrapId($id)
    {
        $this->giftWrapId = $id;
        return $this;
    }

    /**
     * Getting back the gift wrap id
     *
     * @return int
     */
    public function getGiftWrapId()
    {
        return $this->giftWrapId;
    }

    /**
     * Setting the flat items
     *
     * @param mixed $items
     * @return $this
     */
    public function setFlatItems($items)
    {
        $this->flatItems = $items;
        return $this;
    }

    /**
     * Getting back the flat items
     *
     * @return mixed
     */
    public function getFlatItems()
    {
        return $this->flatItems;
    }

    /**
     * Setting the used gift card amount
     *
     * @param float $amount
     * @return $this
     */
    public function setUsedGiftCardAmount($amount)
    {
        $this->usedGiftCardAmount = $amount;
        return $this;
    }

    /**
     * Getting back the used gift card amount
     *
     * @return float
     */
    public function getUsedGiftCardAmount()
    {
        return $this->usedGiftCardAmount;
    }

    /**
     * Setting the totals
     *
     * @param array $totals
     * @return $this
     */
    public function setTotals($totals)
    {
        $this->totals = $totals;
        return $this;
    }

    /**
     * Getting back the totals
     *
     * @return array
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * Setting the customer tax class id
     *
     * @param int $id
     * @return $this
     */
    public function setCustomerTaxClassId($id)
    {
        $this->customerTaxClassId = $id;
        return $this;
    }

    /**
     * Getting back the customer tax class id
     *
     * @return int
     */
    public function getCustomerTaxClassId()
    {
        return $this->customerTaxClassId;
    }

    /**
     * Setting the base subtotal
     *
     * @param float $subtotal
     * @return $this
     */
    public function setBaseSubtotal($subtotal)
    {
        $this->baseSubtotal = $subtotal;
        return $this;
    }

    /**
     * Getting back the base subtotal
     *
     * @return float
     */
    public function getBaseSubtotal()
    {
        return $this->baseSubtotal;
    }

    /**
     * Setting the base subtotal with discount
     *
     * @param float $subtotal
     * @return $this
     */
    public function setBaseSubtotalWithDiscount($subtotal)
    {
        $this->baseSubtotalWithDiscount = $subtotal;
        return $this;
    }

    /**
     * Getting back the base subtotal with discount
     *
     * @return float
     */
    public function getBaseSubtotalWithDiscount()
    {
        return $this->baseSubtotalWithDiscount;
    }

    /**
     * Setting the discount description
     *
     * @param string $description
     * @return $this
     */
    public function setDiscountDescription($description)
    {
        $this->discountDescription = $description;
        return $this;
    }

    /**
     * Getting back the discount description
     *
     * @return string
     */
    public function getDiscountDescription()
    {
        return $this->discountDescription;
    }

    /**
     * Setting the coupon code
     *
     * @param string $code
     * @return $this
     */
    public function setCouponCode($code)
    {
        $this->couponCode = $code;
        return $this;
    }

    /**
     * Getting back the coupon code
     *
     * @return string
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * Setting the discount amount
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount)
    {
        $this->discountAmount = $amount;
        return $this;
    }

    /**
     * Getting back the discount amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Setting the items
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Getting back the items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Setting the store
     *
     * @param StoreInterface $store
     * @return $this
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Getting back the store
     *
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Setting the shipping address
     *
     * @param mixed $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     * Getting back the shipping address
     *
     * @return mixed
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Setting the billing address
     *
     * @param mixed $billingAddress
     * @return $this
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /**
     * Getting back the billing address
     *
     * @return mixed
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }
}
