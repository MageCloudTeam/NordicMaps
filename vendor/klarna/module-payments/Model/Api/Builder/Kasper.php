<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder;

use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Klarna\Kp\Model\Configuration\ApiValidation;
use Klarna\Kp\Model\Configuration\Payment;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Helper\ConfigHelper;
use Klarna\Base\Helper\DataConverter;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Kco\Model\Payment\Kco;
use Klarna\Kp\Api\Data\RequestInterface;
use Klarna\Kp\Model\Api\Builder\Customer\Generator;
use Klarna\Kp\Model\Api\Request;
use Klarna\Kp\Model\Payment\Kp;
use Klarna\Orderlines\Model\Fpt\Calculator;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Copy;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Url;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address;
use Klarna\Base\Api\BuilderInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @api
 */
class Kasper implements BuilderInterface
{
    /**
     * @var Request\Builder
     */
    private $requestBuilder;
    /**
     * @var DataConverter
     */
    private $dataConverter;

    /** @var Calculator $calculator */
    private $calculator;
    /**
     * @var Parameter
     */
    private $parameter;
    /**
     * @var Url
     */
    private $url;
    /**
     * @var ConfigHelper
     */
    private $configHelper;
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;
    /**
     * @var DateTime
     */
    private $coreDate;
    /**
     * @var Copy
     */
    private $objCopyService;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var Generator
     */
    private Generator $customerGenerator;
    /**
     * @var Random
     */
    private Random $random;
    /**
     * @var string
     */
    private string $authCallbackToken = '';
    /**
     * @var ApiValidation
     */
    private ApiValidation $apiValidation;
    /**
     * @var Payment
     */
    private Payment $paymentConfiguration;
    /**
     * @var MagentoToKlarnaLocaleMapper
     */
    private MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper;

    /**
     * @param Url                                         $url
     * @param ConfigHelper                                $configHelper
     * @param Calculator                                  $calculator
     * @param DirectoryHelper                             $directoryHelper
     * @param DateTime                                    $coreDate
     * @param Copy                                        $objCopyService
     * @param DataConverter                               $dataConverter
     * @param Request\Builder                             $requestBuilder
     * @param DataObjectFactory                           $dataObjectFactory
     * @param Parameter                                   $parameter
     * @param Generator                                   $customerGenerator
     * @param Random                                      $random
     * @param ApiValidation                               $apiValidation
     * @param Payment                                     $paymentConfiguration
     * @param MagentoToKlarnaLocaleMapper                 $magentoToKlarnaLocaleMapper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codeCoverageIgnore
     */
    public function __construct(
        Url $url,
        ConfigHelper $configHelper,
        Calculator $calculator,
        DirectoryHelper $directoryHelper,
        DateTime $coreDate,
        Copy $objCopyService,
        DataConverter $dataConverter,
        Request\Builder $requestBuilder,
        DataObjectFactory $dataObjectFactory,
        Parameter $parameter,
        Generator $customerGenerator,
        Random $random = null,
        ApiValidation $apiValidation = null,
        Payment $paymentConfiguration = null,
        MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper = null
    ) {
        $this->dataConverter = $dataConverter;
        $this->requestBuilder = $requestBuilder;
        $this->calculator = $calculator;
        $this->parameter = $parameter;
        $this->url                = $url;
        $this->configHelper       = $configHelper;
        $this->directoryHelper    = $directoryHelper;
        $this->coreDate           = $coreDate;
        $this->objCopyService     = $objCopyService;
        $this->dataObjectFactory  = $dataObjectFactory;
        $this->parameter          = $parameter;
        $this->customerGenerator  = $customerGenerator;
        $this->random = $random ?: ObjectManager::getInstance()->get(
            Random::class
        );
        $this->apiValidation = $apiValidation ?: ObjectManager::getInstance()->get(
            ApiValidation::class
        );
        $this->paymentConfiguration = $paymentConfiguration ?: ObjectManager::getInstance()->get(
            Payment::class
        );
        $this->magentoToKlarnaLocaleMapper = $magentoToKlarnaLocaleMapper ?: ObjectManager::getInstance()->get(
            MagentoToKlarnaLocaleMapper::class
        );
    }

    /**
     * Getting back the auth callback token
     *
     * @return string
     */
    public function getAuthCallbackToken(): string
    {
        return $this->authCallbackToken;
    }

    /**
     * Generate Create/Update Request
     *
     * @param CartInterface $quote
     * @return $this
     * @throws KlarnaApiException
     * @throws KlarnaException
     * @throws LocalizedException
     */
    private function generateCreateUpdate(CartInterface $quote)
    {
        $this->authCallbackToken = $this->random->getUniqueHash();

        $requiredAttributes = [
            'purchase_country',
            'purchase_currency',
            'locale',
            'order_amount',
            'orderlines'
        ];

        $store = $quote->getStore();
        $options = $this->paymentConfiguration->getDesign($store);

        /**
         * Pre-fill customer details
         */
        $this->prefillAddresses($quote, $store);

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        $country = $this->getCountry($quote);

        $orderLines = $this->parameter->getOrderLines();
        $this->requestBuilder->setPurchaseCountry($country)
            ->setPurchaseCurrency($quote->getBaseCurrencyCode())
            ->setLocale($this->magentoToKlarnaLocaleMapper->getLocale())
            ->setOptions($options)
            ->setOrderAmount((int)$this->dataConverter->toApiFloat($address->getBaseGrandTotal()))
            ->addOrderlines($orderLines)
            ->setOrderTaxAmount($this->getOrderTaxAmount($orderLines))
            ->setMerchantUrls($this->processMerchantUrls())
            ->setCustomer($this->customerGenerator->getBasicData($quote));

        $validator = $this->requestBuilder->getValidator();
        $validator->isRequiredValueMissing($requiredAttributes, self::GENERATE_TYPE_CREATE);
        $validator->isSumOrderLinesMatchingOrderAmount();

        return $this;
    }

    /**
     * Getting back the order tax amount
     *
     * @param array $orderlines
     * @return int
     */
    private function getOrderTaxAmount(array $orderlines): int
    {
        $result = 0;
        foreach ($orderlines as $item) {
            if ($item['type'] === 'sales_tax') {
                return (int) $item['total_amount'];
            }
            $result += $item['total_tax_amount'];
        }

        return (int) $result;
    }

    /**
     * Prefilling the address
     *
     * @param CartInterface $quote
     * @param StoreInterface $store
     */
    public function prefillAddresses(CartInterface $quote, StoreInterface $store)
    {
        if (!$this->paymentConfiguration->isDataSharingEnabled($store)) {
            return;
        }
        if (!$this->apiValidation->isKpEndpointSelectedForUsMarket($store)) {
            return;
        }
        $billingAddress = $this->getAddressData($quote, Address::TYPE_BILLING);
        if (!isset($billingAddress['country']) || $billingAddress['country'] !== 'US') {
            return;
        }
        $this->addBillingAddress($billingAddress);
        $this->addShippingAddress($this->getAddressData($quote, Address::TYPE_SHIPPING));
    }

    /**
     * Adding the billing address
     *
     * @param array $address
     */
    private function addBillingAddress(array $address)
    {
        if ($this->validateAddress($address)) {
            $this->requestBuilder->setBillingAddress($address);
        }
    }

    /**
     * Validating the address
     *
     * @param array $address
     * @return bool
     */
    private function validateAddress(array $address = null)
    {
        if ($address === null) {
            return false;
        }
        if (!is_array($address)) {
            return false;
        }
        if (!isset($address['email'])) {
            return false;
        }
        return true;
    }

    /**
     * Adding the shipping address
     *
     * @param array $address
     */
    private function addShippingAddress(array $address)
    {
        if ($this->validateAddress($address)) {
            $this->requestBuilder->setShippingAddress($address);
        }
    }

    /**
     * Pre-process Merchant URLs
     *
     * @param bool $nosid
     * @param bool $forced_secure
     * @return string[]
     */
    public function processMerchantUrls(bool $nosid = true, bool $forced_secure = true)
    {
        $urlParams = [
            '_nosid'         => $nosid,
            '_forced_secure' => $forced_secure
        ];

        return [
            'confirmation' => $this->url->getDirectUrl('checkout/onepage/success', $urlParams),
            'notification' => preg_replace(
                '/\/id\/{checkout\.order\.id}/',
                '',
                $this->url->getDirectUrl(
                    'klarna/api/disabled',
                    $urlParams
                )
            ),
            'authorization' => $this->url->getDirectUrl(
                'checkout/klarna/authorize?token=' . $this->authCallbackToken,
                $urlParams
            )
        ];
    }

    /**
     * Generate place order body
     *
     * @param CartInterface $quote
     * @return Kasper
     * @throws KlarnaException
     * @throws KlarnaApiException
     * @throws LocalizedException
     */
    private function generatePlace(CartInterface $quote)
    {
        $requiredAttributes = [
            'purchase_country',
            'purchase_currency',
            'locale',
            'order_amount',
            'orderlines',
            'merchant_urls',
            'billing_address',
            'shipping_address'
        ];

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        /**
         * Get customer addresses (shipping and billing)
         */
        $this->addBillingAddress($this->getAddressData($quote, Address::TYPE_BILLING));
        $this->addShippingAddress($this->getAddressData($quote, Address::TYPE_SHIPPING));

        $orderLines = $this->parameter->getOrderLines();
        $this->requestBuilder->setPurchaseCountry($this->getCountry($quote))
            ->setPurchaseCurrency($quote->getBaseCurrencyCode())
            ->setLocale($this->magentoToKlarnaLocaleMapper->getLocale())
            ->setOrderAmount((int)$this->dataConverter->toApiFloat($address->getBaseGrandTotal()))
            ->addOrderlines($orderLines)
            ->setOrderTaxAmount($this->getOrderTaxAmount($orderLines))
            ->setMerchantUrls($this->processMerchantUrls())
            ->setMerchantReferences($this->getMerchantReferences($quote));

        $validator = $this->requestBuilder->getValidator();
        $validator->isRequiredValueMissing($requiredAttributes, self::GENERATE_TYPE_PLACE);
        $validator->isSumOrderLinesMatchingOrderAmount();

        return $this;
    }

    /**
     * Get request
     *
     * @return RequestInterface
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getRequest()
    {
        return $this->requestBuilder->getRequest();
    }

    /**
     * Generating the "create" request
     *
     * @param CartInterface $quote
     * @return $this|Kasper
     * @throws KlarnaApiException
     * @throws KlarnaException
     * @throws LocalizedException
     */
    public function generateCreateRequest(CartInterface $quote)
    {
        $this->parameter->resetOrderLines();
        $this->parameter->getOrderLineProcessor()
            ->processByQuote($this->parameter, $quote);

        return $this->generateCreateUpdate($quote);
    }

    /**
     * Generating the "update" request
     *
     * @param CartInterface $quote
     * @return $this|Kasper
     * @throws KlarnaApiException
     * @throws KlarnaException
     * @throws LocalizedException
     */
    public function generateUpdateRequest(CartInterface $quote)
    {
        $this->parameter->resetOrderLines();
        $this->parameter->getOrderLineProcessor()
            ->processByQuote($this->parameter, $quote);

        return $this->generateCreateUpdate($quote);
    }

    /**
     * @inheritdoc
     */
    public function generatePlaceOrderRequest(CartInterface $quote)
    {
        $this->parameter->resetOrderLines();
        $this->parameter->getOrderLineProcessor()
            ->processByQuote($this->parameter, $quote);

        return $this->generatePlace($quote);
    }

    /**
     * @inheritdoc
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Get order lines as array
     *
     * @param StoreInterface $store
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOrderLines(StoreInterface $store): array
    {
        return $this->parameter->getOrderLines();
    }

    /**
     * Get merchant references
     *
     * @param CartInterface $quote
     * @return DataObject
     */
    public function getMerchantReferences(CartInterface $quote): DataObject
    {
        return $this->dataObjectFactory->create([
            'data' => [
                'merchant_reference_1' => $quote->getReservedOrderId(),
                'merchant_reference_2' => ''
            ]
        ]);
    }

    /**
     * Populate prefill values
     *
     * @param array          $create
     * @param CartInterface  $quote
     * @return mixed
     */
    public function prefill(array $create, CartInterface $quote)
    {
        /**
         * Customer
         */
        $create['customer'] = $this->getCustomerData($quote);

        /**
         * Billing Address
         */
        $create['billing_address'] = $this->getAddressData($quote, Address::TYPE_BILLING);

        /**
         * Shipping Address
         */
        if (isset($create['billing_address'])) {
            $create['shipping_address'] = $this->getAddressData($quote, Address::TYPE_SHIPPING);
        }
        return $create;
    }

    /**
     * Get customer details
     *
     * @param CartInterface $quote
     * @return array
     */
    public function getCustomerData(CartInterface $quote): ?array
    {
        if (!$quote->getCustomerIsGuest() && $quote->getCustomerDob()) {
            return [
                'date_of_birth' => $this->coreDate->date('Y-m-d', $quote->getCustomerDob())
            ];
        }

        return null;
    }

    /**
     * Auto fill user address details
     *
     * @param CartInterface $quote
     * @param string        $type
     *
     * @return array
     */
    private function getAddressData(CartInterface $quote, $type = null): array
    {
        $result = [];
        if ($quote->getCustomerEmail()) {
            $result['email'] = $quote->getCustomerEmail();
        }

        $address = $quote->getShippingAddress();
        if ($type === Address::TYPE_BILLING || $quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        }

        return $this->processAddress($result, $quote, $address);
    }

    /**
     * Processing the address
     *
     * @param array         $result
     * @param CartInterface $quote
     * @param Address       $address
     * @return array
     */
    private function processAddress(array $result, CartInterface $quote, Address $address = null): array
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

            /*
             * Making sure the billing address' organization name is empty
             * during requests to prevent error when B2B is disabled
             */
            if ($this->shouldClearOrganizationName($quote, $address)) {
                $resultObject->setOrganizationName('');
            }
            if ($address->getCountry() === 'US') {
                $resultObject->setRegion($address->getRegionCode());
            }
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
    private function prepareStreetAddressArray(DataObject $resultObject): array
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
     * Verifies if we should clear the organization name from the address object
     *
     * @param CartInterface $quote
     * @param Address       $address
     * @return bool
     */
    private function shouldClearOrganizationName(CartInterface $quote, Address $address): bool
    {
        $store = $quote->getStore();
        $b2bEnabled = $this->paymentConfiguration->isB2bEnabled($store);
        $isBillingAddress = $address->getAddressType() === Address::TYPE_BILLING;

        return !$b2bEnabled && ($isBillingAddress || !$quote->getIsVirtual());
    }

    /**
     * Find and return country based on from quote billing/shipping addresses or default country
     *
     * @param CartInterface $quote
     * @return string
     */
    public function getCountry(CartInterface $quote): string
    {
        $country = $quote->getBillingAddress()->getCountry();
        if (empty($country)) {
            $country = $quote->getShippingAddress()->getCountry();
        }
        if (empty($country)) {
            $country = $this->directoryHelper->getDefaultCountry($quote->getStore());
        }

        return $country;
    }
}
