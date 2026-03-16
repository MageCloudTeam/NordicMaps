<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Orderlines\Model\Container;

use Klarna\Base\Helper\ConfigHelper;
use Klarna\Kco\Model\Payment\Kco;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\Validator\General;
use Magento\Customer\Model\Data\Address as CustomerAddress;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Url;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Base\Model\Api\OrderLineProcessor;

/**
 * Base class to generate API configuration
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @internal
 */
class Parameter
{
    /**
     * @var bool
     */
    private $shippingLineEnabled = true;
    /**
     * @var array
     */
    private $orderLines = [];
    /**
     * @var array
     */
    private $request = [];
    /**
     * @var ConfigHelper
     */
    private $configHelper;
    /**
     * @var Url
     */
    private $url;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $coreDate;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var DataObject\Copy
     */
    private $objCopyService;
    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    private $addressRegistry;
    /**
     * @var OrderLineProcessor $orderLineProcessor
     */
    private $orderLineProcessor;
    /**
     * @var float $shippingUnitPrice
     */
    private $shippingUnitPrice;
    /**
     * @var float $shippingTaxRate
     */
    private $shippingTaxRate;
    /**
     * @var float $shippingTotalAmount
     */
    private $shippingTotalAmount;
    /**
     * @var float $shippingTaxAmount
     */
    private $shippingTaxAmount;
    /**
     * @var float $shippingDiscountAmount
     */
    private $shippingDiscountAmount;
    /**
     * @var string $shippingTitle
     */
    private $shippingTitle;
    /**
     * @var string $shippingReference
     */
    private $shippingReference;
    /**
     * @var float $discountUnitPrice
     */
    private $discountUnitPrice;
    /**
     * @var float $discountTaxRate
     */
    private $discountTaxRate;
    /**
     * @var float $discountTotalAmount
     */
    private $discountTotalAmount;
    /**
     * @var float $discountTaxAmount
     */
    private $discountTaxAmount;
    /**
     * @var string $discountTitle
     */
    private $discountTitle;
    /**
     * @var string $discountReference
     */
    private $discountReference;
    /**
     * @var float $taxUnitPrice
     */
    private $taxUnitPrice;
    /**
     * @var float $totalTaxAmount
     */
    private $totalTaxAmount;
    /**
     * @var array $items
     */
    private $items;
    /**
     * @var float $customerBalanceUnitPrice
     */
    private $customerBalanceUnitPrice;
    /**
     * @var float $customerBalanceTaxRate
     */
    private $customerBalanceTaxRate;
    /**
     * @var float $customerBalanceTotalAmount
     */
    private $customerBalanceTotalAmount;
    /**
     * @var float $customerBalanceTaxAmount
     */
    private $customerBalanceTaxAmount;
    /**
     * @var string $customerBalanceTitle
     */
    private $customerBalanceTitle;
    /**
     * @var string $customerBalanceReference
     */
    private $customerBalanceReference;
    /**
     * @var float $giftCardAccountUnitPrice
     */
    private $giftCardAccountUnitPrice;
    /**
     * @var float $giftCardAccountTaxRate
     */
    private $giftCardAccountTaxRate;
    /**
     * @var float $giftCardAccountTotalAmount
     */
    private $giftCardAccountTotalAmount;
    /**
     * @var float $giftCardAccountTaxAmount
     */
    private $giftCardAccountTaxAmount;
    /**
     * @var string $giftCardAccountTitle
     */
    private $giftCardAccountTitle;
    /**
     * @var string $giftCardAccountReference
     */
    private $giftCardAccountReference;
    /**
     * @var array $giftWrapItems
     */
    private $giftWrapItems;
    /**
     * @var float $rewardUnitPrice
     */
    private $rewardUnitPrice;
    /**
     * @var float $rewardTaxRate
     */
    private $rewardTaxRate;
    /**
     * @var float $rewardTotalAmount
     */
    private $rewardTotalAmount;
    /**
     * @var float $rewardTaxAmount
     */
    private $rewardTaxAmount;
    /**
     * @var string $rewardTitle
     */
    private $rewardTitle;
    /**
     * @var string $rewardReference
     */
    private $rewardReference;
    /**
     * @var float $surchargeUnitPrice
     */
    private $surchargeUnitPrice;
    /**
     * @var float $surchargeTotalAmount
     */
    private $surchargeTotalAmount;
    /**
     * @var string $surchargeReference
     */
    private $surchargeReference;
    /**
     * @var string $surchargeName
     */
    private $surchargeName;
    /**
     * @var float $taxTotalAmount
     */
    private $taxTotalAmount;
    /**
     * @var bool $virtual
     */
    private $virtual;
    /**
     * @var General $validator
     */
    private $validator;
    /**
     * @var array
     */
    private array $customData = [];
    /**
     * @var array
     */
    private array $entities;
    /**
     * @var StoreInterface
     */
    private StoreInterface $store;

    /**
     * @param Url                                     $url
     * @param ConfigHelper                            $configHelper
     * @param DateTime\DateTime                       $coreDate
     * @param DataObject\Copy                         $objCopyService
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     * @param DataObjectFactory                       $dataObjectFactory
     * @param OrderLineProcessor                      $orderLineProcessor
     * @param General                                 $validator
     * @param array                                   $entities
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codeCoverageIgnore
     */
    public function __construct(
        Url $url,
        ConfigHelper $configHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\DataObject\Copy $objCopyService,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        DataObjectFactory $dataObjectFactory,
        OrderLineProcessor $orderLineProcessor,
        General $validator,
        array $entities
    ) {
        $this->url                = $url;
        $this->configHelper       = $configHelper;
        $this->coreDate           = $coreDate;
        $this->objCopyService     = $objCopyService;
        $this->addressRegistry    = $addressRegistry;
        $this->dataObjectFactory  = $dataObjectFactory;
        $this->orderLineProcessor = $orderLineProcessor;
        $this->validator          = $validator;
        $this->entities           = $entities;
    }

    /**
     * Setting the virtual flag
     *
     * @param bool $value
     * @return $this
     */
    public function setVirtualFlag($value)
    {
        $this->virtual = $value;
        return $this;
    }

    /**
     * Returns true if we have virtual products
     *
     * @return bool
     */
    public function isVirtual()
    {
        return (bool) $this->virtual;
    }

    /**
     * Getting back the tax total amount
     *
     * @return float
     */
    public function getTaxTotalAmount()
    {
        return $this->taxTotalAmount;
    }

    /**
     * Setting the tax total amount
     *
     * @param float $taxTotalAmount
     * @return $this
     */
    public function setTaxTotalAmount($taxTotalAmount)
    {
        $this->taxTotalAmount = $taxTotalAmount;
        return $this;
    }

    /**
     * Getting back the surcharge unit price
     *
     * @return float
     */
    public function getSurchargeUnitPrice()
    {
        return $this->surchargeUnitPrice;
    }

    /**
     * Setting the surcharge unit price
     *
     * @param float $surchargeUnitPrice
     * @return $this
     */
    public function setSurchargeUnitPrice($surchargeUnitPrice)
    {
        $this->surchargeUnitPrice = $surchargeUnitPrice;
        return $this;
    }

    /**
     * Getting back the surcharge total amount
     *
     * @return float
     */
    public function getSurchargeTotalAmount()
    {
        return $this->surchargeTotalAmount;
    }

    /**
     * Setting the surcharge total amount
     *
     * @param float $surchargeTotalAmount
     * @return $this
     */
    public function setSurchargeTotalAmount($surchargeTotalAmount)
    {
        $this->surchargeTotalAmount = $surchargeTotalAmount;
        return $this;
    }

    /**
     * Getting back the surcharge reference
     *
     * @return string
     */
    public function getSurchargeReference()
    {
        return $this->surchargeReference;
    }

    /**
     * Setting the surcharge reference
     *
     * @param string $surchargeReference
     * @return $this
     */
    public function setSurchargeReference($surchargeReference)
    {
        $this->surchargeReference = $surchargeReference;
        return $this;
    }

    /**
     * Getting back the surcharge name
     *
     * @return string
     */
    public function getSurchargeName()
    {
        return $this->surchargeName;
    }

    /**
     * Setting the surcharge name
     *
     * @param string $surchargeName
     * @return $this
     */
    public function setSurchargeName($surchargeName)
    {
        $this->surchargeName = $surchargeName;
        return $this;
    }

    /**
     * Getting back the reward unit price
     *
     * @return float
     */
    public function getRewardUnitPrice()
    {
        return $this->rewardUnitPrice;
    }

    /**
     * Setting the reward unit price
     *
     * @param float $rewardUnitPrice
     * @return $this
     */
    public function setRewardUnitPrice($rewardUnitPrice)
    {
        $this->rewardUnitPrice = $rewardUnitPrice;
        return $this;
    }

    /**
     * Getting back the reward tax rate
     *
     * @return float
     */
    public function getRewardTaxRate()
    {
        return $this->rewardTaxRate;
    }

    /**
     * Setting the reward tax rate
     *
     * @param float $rewardTaxRate
     * @return $this
     */
    public function setRewardTaxRate($rewardTaxRate)
    {
        $this->rewardTaxRate = $rewardTaxRate;
        return $this;
    }

    /**
     * Getting back the reward total amount
     *
     * @return float
     */
    public function getRewardTotalAmount()
    {
        return $this->rewardTotalAmount;
    }

    /**
     * Setting the reward total amount
     *
     * @param float $rewardTotalAmount
     * @return $this
     */
    public function setRewardTotalAmount($rewardTotalAmount)
    {
        $this->rewardTotalAmount = $rewardTotalAmount;
        return $this;
    }

    /**
     * Getting back the reward tax amount
     *
     * @return float
     */
    public function getRewardTaxAmount()
    {
        return $this->rewardTaxAmount;
    }

    /**
     * Setting the reward tax amount
     *
     * @param float $rewardTaxAmount
     * @return $this
     */
    public function setRewardTaxAmount($rewardTaxAmount)
    {
        $this->rewardTaxAmount = $rewardTaxAmount;
        return $this;
    }

    /**
     * Getting back the reward title
     *
     * @return string
     */
    public function getRewardTitle()
    {
        return $this->rewardTitle;
    }

    /**
     * Setting the reward title
     *
     * @param string $rewardTitle
     * @return $this
     */
    public function setRewardTitle($rewardTitle)
    {
        $this->rewardTitle = $rewardTitle;
        return $this;
    }

    /**
     * Getting back the reward reference
     *
     * @return string
     */
    public function getRewardReference()
    {
        return $this->rewardReference;
    }

    /**
     * Setting th reward reference
     *
     * @param string $rewardReference
     * @return $this
     */
    public function setRewardReference($rewardReference)
    {
        $this->rewardReference = $rewardReference;
        return $this;
    }

    /**
     * Setting the gift wrap items
     *
     * @param array $giftWrapItems
     * @return $this
     */
    public function setGiftWrapItems(array $giftWrapItems)
    {
        $this->giftWrapItems = $giftWrapItems;
        return $this;
    }

    /**
     * Getting back the gift wrap items
     *
     * @return array
     */
    public function getGiftWrapItems()
    {
        return $this->giftWrapItems;
    }

    /**
     * Getting back the total amount of the customer balance
     *
     * @return float
     */
    public function getCustomerBalanceTotalAmount()
    {
        return $this->customerBalanceTotalAmount;
    }

    /**
     * Setting the total amount of the customer balance
     *
     * @param float $customerBalanceTotalAmount
     * @return $this
     */
    public function setCustomerBalanceTotalAmount($customerBalanceTotalAmount)
    {
        $this->customerBalanceTotalAmount = $customerBalanceTotalAmount;
        return $this;
    }

    /**
     * Getting back the tax amount of the customer balance
     *
     * @return float
     */
    public function getCustomerBalanceTaxAmount()
    {
        return $this->customerBalanceTaxAmount;
    }

    /**
     * Setting the tax amount of the customer balance
     *
     * @param float $customerBalanceTaxAmount
     * @return $this
     */
    public function setCustomerBalanceTaxAmount($customerBalanceTaxAmount)
    {
        $this->customerBalanceTaxAmount = $customerBalanceTaxAmount;
        return $this;
    }

    /**
     * Getting back the title for the customer balance
     *
     * @return string
     */
    public function getCustomerBalanceTitle()
    {
        return $this->customerBalanceTitle;
    }

    /**
     * Setting the title of the customer balance
     *
     * @param string $customerBalanceTitle
     * @return $this
     */
    public function setCustomerBalanceTitle($customerBalanceTitle)
    {
        $this->customerBalanceTitle = $customerBalanceTitle;
        return $this;
    }

    /**
     * Getting back the customer balance reference
     *
     * @return string
     */
    public function getCustomerBalanceReference()
    {
        return $this->customerBalanceReference;
    }

    /**
     * Setting the customer balance reference
     *
     * @param string $customerBalanceReference
     * @return $this
     */
    public function setCustomerBalanceReference($customerBalanceReference)
    {
        $this->customerBalanceReference = $customerBalanceReference;
        return $this;
    }

    /**
     * Getting back the customer balance tax rate
     *
     * @return float
     */
    public function getCustomerBalanceTaxRate()
    {
        return $this->customerBalanceTaxRate;
    }

    /**
     * Setting the customer balance tax rate
     *
     * @param float $customerBalanceTaxRate
     * @return $this
     */
    public function setCustomerBalanceTaxRate($customerBalanceTaxRate)
    {
        $this->customerBalanceTaxRate = $customerBalanceTaxRate;
        return $this;
    }

    /**
     * Setting the unit price for the customer balance
     *
     * @param float $price
     * @return $this
     */
    public function setCustomerBalanceUnitPrice($price)
    {
        $this->customerBalanceUnitPrice = $price;
        return $this;
    }

    /**
     * Getting back the unit price of the customer balance.
     *
     * @return float
     */
    public function getCustomerBalanceUnitPrice()
    {
        return $this->customerBalanceUnitPrice;
    }

    /**
     * Getting back the gift card account unit price
     *
     * @return float
     */
    public function getGiftCardAccountUnitPrice()
    {
        return $this->giftCardAccountUnitPrice;
    }

    /**
     * Setting the gift card account unit price
     *
     * @param float $giftCardAccountUnitPrice
     * @return $this
     */
    public function setGiftCardAccountUnitPrice($giftCardAccountUnitPrice)
    {
        $this->giftCardAccountUnitPrice = $giftCardAccountUnitPrice;
        return $this;
    }

    /**
     * Getting back the gift card account tax rate
     *
     * @return float
     */
    public function getGiftCardAccountTaxRate()
    {
        return $this->giftCardAccountTaxRate;
    }

    /**
     * Setting the gift card account tax rate
     *
     * @param float $giftCardAccountTaxRate
     * @return $this
     */
    public function setGiftCardAccountTaxRate($giftCardAccountTaxRate)
    {
        $this->giftCardAccountTaxRate = $giftCardAccountTaxRate;
        return $this;
    }

    /**
     * Getting back the gift card account total amount
     *
     * @return float
     */
    public function getGiftCardAccountTotalAmount()
    {
        return $this->giftCardAccountTotalAmount;
    }

    /**
     * Setting the gift card account total amount
     *
     * @param float $giftCardAccountTotalAmount
     * @return $this
     */
    public function setGiftCardAccountTotalAmount($giftCardAccountTotalAmount)
    {
        $this->giftCardAccountTotalAmount = $giftCardAccountTotalAmount;
        return $this;
    }

    /**
     * Getting back the gift card account tax amount
     *
     * @return float
     */
    public function getGiftCardAccountTaxAmount()
    {
        return $this->giftCardAccountTaxAmount;
    }

    /**
     * Setting the gift card account tax amount
     *
     * @param float $giftCardAccountTaxAmount
     * @return $this
     */
    public function setGiftCardAccountTaxAmount($giftCardAccountTaxAmount)
    {
        $this->giftCardAccountTaxAmount = $giftCardAccountTaxAmount;
        return $this;
    }

    /**
     * Getting back the gift card account title
     *
     * @return string
     */
    public function getGiftCardAccountTitle()
    {
        return $this->giftCardAccountTitle;
    }

    /**
     * Setting the gift card account title
     *
     * @param string $giftCardAccountTitle
     * @return $this
     */
    public function setGiftCardAccountTitle($giftCardAccountTitle)
    {
        $this->giftCardAccountTitle = $giftCardAccountTitle;
        return $this;
    }

    /**
     * Getting back the gift card account reference
     *
     * @return string
     */
    public function getGiftCardAccountReference()
    {
        return $this->giftCardAccountReference;
    }

    /**
     * Setting the gift card account reference
     *
     * @param string $giftCardAccountReference
     * @return $this
     */
    public function setGiftCardAccountReference($giftCardAccountReference)
    {
        $this->giftCardAccountReference = $giftCardAccountReference;
        return $this;
    }

    /**
     * Setting the shipping unit price
     *
     * @param float $price
     * @return $this
     */
    public function setShippingUnitPrice($price)
    {
        $this->shippingUnitPrice = $price;
        return $this;
    }

    /**
     * Getting back the shipping unit price
     *
     * @return float
     */
    public function getShippingUnitPrice()
    {
        return $this->shippingUnitPrice;
    }

    /**
     * Setting the shipping tax rate
     *
     * @param float $rate
     * @return $this
     */
    public function setShippingTaxRate($rate)
    {
        $this->shippingTaxRate = $rate;
        return $this;
    }

    /**
     * Getting back the shipping tax rate
     *
     * @return float
     */
    public function getShippingTaxRate()
    {
        return $this->shippingTaxRate;
    }

    /**
     * Setting the shipping total amount
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingTotalAmount($amount)
    {
        $this->shippingTotalAmount = $amount;
        return $this;
    }

    /**
     * Getting back the shipping total amount
     *
     * @return float
     */
    public function getShippingTotalAmount()
    {
        return $this->shippingTotalAmount;
    }

    /**
     * Setting the shipping tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingTaxAmount($amount)
    {
        $this->shippingTaxAmount = $amount;
        return $this;
    }

    /**
     * Getting back the shipping tax amount
     *
     * @return float
     */
    public function getShippingTaxAmount()
    {
        return $this->shippingTaxAmount;
    }

    /**
     * Setting the shipping discount amount
     *
     * @param float $amount
     * @return $this
     */
    public function setShippingDiscountAmount(float $amount): Parameter
    {
        $this->shippingDiscountAmount = $amount;
        return $this;
    }

    /**
     * Getting back the shipping discount amount
     *
     * @return float
     */
    public function getShippingDiscountAmount(): float
    {
        return $this->shippingDiscountAmount;
    }

    /**
     * Setting the shipping title
     *
     * @param string $title
     * @return $this
     */
    public function setShippingTitle($title)
    {
        $this->shippingTitle = $title;
        return $this;
    }

    /**
     * Getting back the shipping title
     *
     * @return string
     */
    public function getShippingTitle()
    {
        return $this->shippingTitle;
    }

    /**
     * Setting the shipping reference
     *
     * @param string $reference
     * @return $this
     */
    public function setShippingReference($reference)
    {
        $this->shippingReference = $reference;
        return $this;
    }

    /**
     * Getting back the shipping reference
     *
     * @return string
     */
    public function getShippingReference()
    {
        return $this->shippingReference;
    }

    /**
     * Setting the discount unit price
     *
     * @param float $price
     * @return $this
     */
    public function setDiscountUnitPrice($price)
    {
        $this->discountUnitPrice = $price;
        return $this;
    }

    /**
     * Getting bck the discount unit price
     *
     * @return float
     */
    public function getDiscountUnitPrice()
    {
        return $this->discountUnitPrice;
    }

    /**
     * Setting the discount tax rate
     *
     * @param float $rate
     * @return $this
     */
    public function setDiscountTaxRate($rate)
    {
        $this->discountTaxRate = $rate;
        return $this;
    }

    /**
     * Getting back the discount tax rate
     *
     * @return float
     */
    public function getDiscountTaxRate()
    {
        return $this->discountTaxRate;
    }

    /**
     * Setting the discount total amount
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountTotalAmount($amount)
    {
        $this->discountTotalAmount = $amount;
        return $this;
    }

    /**
     * Getting back the discount total amount
     *
     * @return float
     */
    public function getDiscountTotalAmount()
    {
        return $this->discountTotalAmount;
    }

    /**
     * Setting the discount tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountTaxAmount($amount)
    {
        $this->discountTaxAmount = $amount;
        return $this;
    }

    /**
     * Getting back the discount tax amount
     *
     * @return float
     */
    public function getDiscountTaxAmount()
    {
        return $this->discountTaxAmount;
    }

    /**
     * Setting the discount title
     *
     * @param string $title
     * @return $this
     */
    public function setDiscountTitle($title)
    {
        $this->discountTitle = $title;
        return $this;
    }

    /**
     * Getting back the discount title
     *
     * @return string
     */
    public function getDiscountTitle()
    {
        return $this->discountTitle;
    }

    /**
     * Setting the discount reference
     *
     * @param string $reference
     * @return $this
     */
    public function setDiscountReference($reference)
    {
        $this->discountReference = $reference;
        return $this;
    }

    /**
     * Getting back the discount reference
     *
     * @return string
     */
    public function getDiscountReference()
    {
        return $this->discountReference;
    }

    /**
     * Setting the tax unit price
     *
     * @param float $price
     * @return $this
     */
    public function setTaxUnitPrice($price)
    {
        $this->taxUnitPrice = $price;
        return $this;
    }

    /**
     * Getting back the tax unit price
     *
     * @return float
     */
    public function getTaxUnitPrice()
    {
        return $this->taxUnitPrice;
    }

    /**
     * Setting the total tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setTotalTaxAmount($amount)
    {
        $this->totalTaxAmount = $amount;
        return $this;
    }

    /**
     * Getting back the total tax amount
     *
     * @return float
     */
    public function getTotalTaxAmount()
    {
        return $this->totalTaxAmount;
    }

    /**
     * Getting back the orderline processor
     *
     * @return OrderLineProcessor
     */
    public function getOrderLineProcessor()
    {
        return $this->orderLineProcessor;
    }

    /**
     * Set generated request
     *
     * @param array  $request
     *
     * @return $this
     */
    public function setRequest(array $request)
    {
        $this->request = $this->cleanNulls($request);

        return $this;
    }

    /**
     * Getting back the request
     *
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Remove items that are not allowed to be null
     *
     * @param array $request
     * @return array
     */
    private function cleanNulls(array $request)
    {
        $disallowNulls = [
            'customer',
            'billing_address',
            'shipping_address',
            'external_payment_methods'
        ];
        foreach ($disallowNulls as $key) {
            if (empty($request[$key])) {
                unset($request[$key]);
            }
        }
        return $request;
    }

    /**
     * Get order lines as array
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderLines(): array
    {
        $this->resetOrderLines();
        foreach ($this->getOrderlineItemEntities() as $model) {
            $model->fetch($this);
        }

        return $this->orderLines;
    }

    /**
     * Add an order line
     *
     * @param array $orderLine
     *
     * @return $this
     */
    public function addOrderLine(array $orderLine)
    {
        $this->orderLines[] = $orderLine;

        return $this;
    }

    /**
     * Remove all order lines
     *
     * @return $this
     */
    public function resetOrderLines()
    {
        $this->orderLines = [];

        return $this;
    }

    /**
     * Get merchant references
     *
     * @param CartInterface $quote
     * @return DataObject
     */
    public function getMerchantReferences(CartInterface $quote)
    {
        $merchantReferences = $this->dataObjectFactory->create([
            'data' => [
                'merchant_reference_1' => $quote->getReservedOrderId(),
                'merchant_reference_2' => ''
            ]
        ]);

        return $merchantReferences;
    }

    /**
     * Get Terms URL
     *
     * @param string $termsUrl
     * @return string
     */
    public function getTermsUrl(string $termsUrl): string
    {
        $termsUrlStart = substr($termsUrl, 0, 5);
        if ($termsUrlStart !== 'http:' && $termsUrlStart !== 'https') {
            return $this->url->getDirectUrl($termsUrl, ['_nosid' => true]);
        }

        return $termsUrl;
    }

    /**
     * Auto fill user address details
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param string                                $type
     *
     * @return array
     */
    public function getAddressData($quote, $type = null)
    {
        $result = [];
        if ($quote->getCustomerEmail()) {
            $result['email'] = $quote->getCustomerEmail();
        }
        $customer = $quote->getCustomer();

        if ($quote->isVirtual() || $type === Address::TYPE_BILLING) {
            $address = $quote->getBillingAddress();

            if ($customer->getId() && !$address->getPostcode()) {
                $address = $this->getCustomerAddress($customer->getDefaultBilling());
            }

            return $this->processAddress($result, $address);
        }

        $address = $quote->getShippingAddress();

        if ($customer->getId() && !$address->getPostcode()) {
            $address = $this->getCustomerAddress($customer->getDefaultShipping());
        }

        return $this->processAddress($result, $address);
    }

    /**
     * Retrieve customer address
     *
     * @param AddressInterface|string $address_id
     * @return CustomerAddress|AddressInterface
     */
    private function getCustomerAddress($address_id)
    {
        if (!$address_id) {
            return null;
        }
        if ($address_id instanceof AddressInterface) {
            return $address_id;
        }
        try {
            return $this->addressRegistry->retrieve($address_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Processing the address
     *
     * @param array $result
     * @param AddressInterface $address
     * @return array
     */
    private function processAddress(array $result, $address = null)
    {
        $resultObject = $this->dataObjectFactory->create(['data' => $result]);
        if ($address) {
            $address->explodeStreetAddress();
            $this->objCopyService->copyFieldsetToTarget(
                'sales_convert_quote_address',
                'to_klarna',
                $address,
                $resultObject
            );
            if ($address->getCountryId() === 'US') {
                $resultObject->setRegion($address->getRegionCode());
            }
            $resultObject->setErrors($this->validator->validate($address));
        }

        $street_address = $this->prepareStreetAddressArray($resultObject);
        $resultObject->setStreetAddress($street_address[0]);
        $resultObject->setData('street_address2', $street_address[1]);

        if (isset($result['email'])) {
            $resultObject->setEmail($result['email']);
        }

        return array_filter($resultObject->toArray());
    }

    /**
     * Preparing the street address
     *
     * @param DataObject $resultObject
     * @return array
     */
    private function prepareStreetAddressArray(DataObject $resultObject)
    {
        $street_address = $resultObject->getStreetAddress();
        if (!is_array($street_address)) {
            $street_address = [$street_address];
        }
        if (count($street_address) === 1) {
            $street_address[] = '';
        }
        return $street_address;
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
     * Determines if a shipping line should be sent or not
     *
     * @return bool
     */
    public function isShippingLineEnabled(): bool
    {
        return $this->shippingLineEnabled;
    }

    /**
     * Setting the shipping line enabled flag value
     *
     * @param bool $value
     * @return $this
     */
    public function setShippingLineEnabled(bool $value): Parameter
    {
        $this->shippingLineEnabled = $value;
        return $this;
    }

    /**
     * Getting back the orderline item entities
     *
     * @return array
     */
    public function getOrderlineItemEntities(): array
    {
        return $this->entities;
    }

    /**
     * Setting custom data. Useful for having a custom orderline (see MAGE-3974)
     *
     * @param array $data
     * @return $this
     */
    public function setCustomData(array $data): Parameter
    {
        $this->customData = $data;
        return $this;
    }

    /**
     * Getting back the custom data
     *
     * @return array
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * Setting the store
     *
     * @param StoreInterface $store
     * @return Parameter
     */
    public function setStore(StoreInterface $store): Parameter
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Getting back the store
     *
     * @return StoreInterface
     */
    public function getStore(): StoreInterface
    {
        return $this->store;
    }
}
