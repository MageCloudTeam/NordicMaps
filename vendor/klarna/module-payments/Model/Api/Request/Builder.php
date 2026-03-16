<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\Data\AddressInterface;
use Klarna\Kp\Api\Data\AttachmentInterface;
use Klarna\Kp\Api\Data\CustomerInterface;
use Klarna\Kp\Api\Data\OptionsInterface;
use Klarna\Kp\Api\Data\OrderlineInterface;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Api\Data\UrlsInterface;
use Klarna\Kp\Model\Api\RequestFactory;
use Magento\Framework\DataObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @internal
 */
class Builder
{
    /**
     * @var string
     */
    private $merchant_reference1;
    /**
     * @var string
     */
    private $merchant_reference2;
    /**
     * @var string
     */
    private $purchase_country;
    /**
     * @var string
     */
    private $purchase_currency;
    /**
     * @var string
     */
    private $locale;
    /**
     * @var int
     */
    private $order_tax_amount = 0;
    /**
     * @var int
     */
    private $order_amount = 0;
    /**
     * @var CustomerInterface
     */
    private $customer;
    /**
     * @var AttachmentInterface
     */
    private $attachment;
    /**
     * @var AddressInterface
     */
    private $billing_address;
    /**
     * @var AddressInterface
     */
    private $shipping_address;
    /**
     * @var UrlsInterface
     */
    private $merchant_urls;
    /**
     * @var OptionsInterface
     */
    private $options;
    /**
     * @var OrderlineInterface[]
     */
    private $orderlines = [];
    /**
     * @var RequestFactory
     */
    private $requestFactory;
    /**
     * @var AddressFactory
     */
    private $addressFactory;
    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var MerchantUrlsFactory
     */
    private $urlFactory;
    /**
     * @var OptionsFactory
     */
    private $optionsFactory;
    /**
     * @var OrderlineFactory
     */
    private $orderlineFactory;
    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @param RequestFactory      $requestFactory
     * @param AddressFactory      $addressFactory
     * @param AttachmentFactory   $attachmentFactory
     * @param CustomerFactory     $customerFactory
     * @param MerchantUrlsFactory $urlFactory
     * @param OptionsFactory      $optionsFactory
     * @param OrderlineFactory    $orderlineFactory
     * @param Validator           $validator
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestFactory $requestFactory,
        AddressFactory $addressFactory,
        AttachmentFactory $attachmentFactory,
        CustomerFactory $customerFactory,
        MerchantUrlsFactory $urlFactory,
        OptionsFactory $optionsFactory,
        OrderlineFactory $orderlineFactory,
        Validator $validator
    ) {
        $this->requestFactory = $requestFactory;
        $this->addressFactory = $addressFactory;
        $this->attachmentFactory = $attachmentFactory;
        $this->customerFactory = $customerFactory;
        $this->urlFactory = $urlFactory;
        $this->optionsFactory = $optionsFactory;
        $this->orderlineFactory = $orderlineFactory;
        $this->validator = $validator;
    }

    /**
     * Setting the attachment
     *
     * @param array $data
     * @return $this
     */
    public function setAttachment(array $data)
    {
        $this->attachment = $this->attachmentFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * Setting the billing address
     *
     * @param array $data
     * @return $this
     */
    public function setBillingAddress(array $data)
    {
        $this->billing_address = $this->addressFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * Setting the shipping address
     *
     * @param array $data
     * @return $this
     */
    public function setShippingAddress(array $data)
    {
        $this->shipping_address = $this->addressFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * Setting the merchant urls
     *
     * @param array $data
     * @return $this
     */
    public function setMerchantUrls(array $data)
    {
        $this->merchant_urls = $this->urlFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * Setting the options
     *
     * @param array $data
     * @return $this
     */
    public function setOptions(array $data)
    {
        $this->options = $this->optionsFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * Adding the orderlines
     *
     * @param array $data
     * @return $this
     */
    public function addOrderlines(array $data)
    {
        foreach ($data as $key => $orderLine) {
            $this->orderlines[$key] = $this->orderlineFactory->create(['data' => $orderLine]);
        }
        return $this;
    }

    /**
     * Setting the merchant references
     *
     * @param DataObject $references
     * @return $this
     */
    public function setMerchantReferences($references)
    {
        $this->merchant_reference1 = $references->getData('merchant_reference_1');
        $this->merchant_reference2 = $references->getData('merchant_reference_2');
        return $this;
    }

    /**
     * Getting the request
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->requestFactory->create([
            'data' => [
                'purchase_country'    => $this->purchase_country,
                'purchase_currency'   => $this->purchase_currency,
                'locale'              => $this->locale,
                'customer'            => $this->customer,
                'options'             => $this->options,
                'order_amount'        => $this->order_amount,
                'order_tax_amount'    => $this->order_tax_amount,
                'order_lines'         => $this->orderlines,
                'urls'                => $this->merchant_urls,
                'attachment'          => $this->attachment,
                'billing_address'     => $this->billing_address,
                'shipping_address'    => $this->shipping_address,
                'merchant_reference1' => $this->merchant_reference1,
                'merchant_reference2' => $this->merchant_reference2
            ]
        ]);
    }

    /**
     * Resetting all data
     */
    public function reset(): void
    {
        $this->purchase_country = null;
        $this->purchase_currency = null;
        $this->locale = null;
        $this->customer = null;
        $this->order_amount = 0;
        $this->order_tax_amount = 0;
        $this->orderlines = [];
        $this->merchant_urls = null;
        $this->attachment = null;
        $this->billing_address = null;
        $this->shipping_address = null;
        $this->merchant_reference1 = null;
        $this->merchant_reference2 = null;
    }

    /**
     * Setting the customer
     *
     * @param array $data
     * @return $this
     */
    public function setCustomer(array $data)
    {
        $this->customer = $this->customerFactory->create(['data' => $data]);
        return $this;
    }

    /**
     * Setting the order amount
     *
     * @param int $amount
     * @return $this
     */
    public function setOrderAmount($amount)
    {
        $this->order_amount = $amount;
        return $this;
    }

    /**
     * Setting the order tax amount
     *
     * @param int $amount
     * @return $this
     */
    public function setOrderTaxAmount($amount)
    {
        $this->order_tax_amount = $amount;
        return $this;
    }

    /**
     * Getting back the validator
     *
     * @return Validator
     */
    public function getValidator(): Validator
    {
        $this->validator->setData($this->getRequest()->toArray());
        return $this->validator;
    }

    /**
     * Getting back the purchase country
     *
     * @return string
     */
    public function getPurchaseCountry()
    {
        return $this->purchase_country;
    }

    /**
     * Setting the purchase country
     *
     * @param string $purchase_country
     * @return Builder
     */
    public function setPurchaseCountry($purchase_country)
    {
        $this->purchase_country = $purchase_country;
        return $this;
    }

    /**
     * Getting back the locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Settng the locale
     *
     * @param string $locale
     * @return Builder
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Getting back the purchase currency
     *
     * @return string
     */
    public function getPurchaseCurrency()
    {
        return $this->purchase_currency;
    }

    /**
     * Setting the purchase currency
     *
     * @param string $purchase_currency
     * @return Builder
     */
    public function setPurchaseCurrency($purchase_currency)
    {
        $this->purchase_currency = $purchase_currency;
        return $this;
    }
}
