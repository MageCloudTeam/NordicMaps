<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Builder;

use Klarna\Base\Exception;
use Klarna\Kp\Model\Api\Builder\Customer\Generator as CustomerGenerator;
use Klarna\Kp\Model\Api\Builder\Nodes\MerchantReferences;
use Klarna\Kp\Model\Api\Builder\Nodes\MerchantUrls;
use Klarna\Kp\Model\Api\Builder\Nodes\Miscellaneous;
use Klarna\Kp\Model\Api\Builder\Nodes\Options;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderAmount;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderLines;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderTaxAmount;
use Klarna\Kp\Model\Api\Builder\Nodes\PurchaseCountry;
use Klarna\Orderlines\Model\Container\Parameter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Builder as AddressBuilder;
use Klarna\Kp\Api\Data\RequestInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @internal
 */
class Request
{
    public const GENERATE_TYPE_CREATE = 'create';
    public const GENERATE_TYPE_UPDATE = 'update';
    public const GENERATE_TYPE_PLACE = 'place';

    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var MerchantUrls
     */
    private MerchantUrls $merchantUrls;
    /**
     * @var OrderLines
     */
    private OrderLines $orderLines;
    /**
     * @var OrderTaxAmount
     */
    private OrderTaxAmount $orderTaxAmount;
    /**
     * @var OrderAmount
     */
    private OrderAmount $orderAmount;
    /**
     * @var PurchaseCountry
     */
    private PurchaseCountry $purchaseCountry;
    /**
     * @var MerchantReferences
     */
    private MerchantReferences $merchantReferences;
    /**
     * @var Options
     */
    private Options $options;
    /**
     * @var CustomerGenerator
     */
    private CustomerGenerator $customerGenerator;
    /**
     * @var Miscellaneous
     */
    private Miscellaneous $miscellaneous;
    /**
     * @var AddressBuilder
     */
    private AddressBuilder $addressBuilder;
    /**
     * @var Parameter
     */
    private Parameter $parameter;

    /**
     * @param Builder $requestBuilder
     * @param MerchantUrls $merchantUrls
     * @param OrderLines $orderLines
     * @param OrderTaxAmount $orderTaxAmount
     * @param OrderAmount $orderAmount
     * @param PurchaseCountry $purchaseCountry
     * @param MerchantReferences $merchantReferences
     * @param Options $options
     * @param CustomerGenerator $customerGenerator
     * @param Miscellaneous $miscellaneous
     * @param AddressBuilder $addressBuilder
     * @param Parameter $parameter
     * @codeCoverageIgnore
     */
    public function __construct(
        Builder $requestBuilder,
        MerchantUrls $merchantUrls,
        OrderLines $orderLines,
        OrderTaxAmount $orderTaxAmount,
        OrderAmount $orderAmount,
        PurchaseCountry $purchaseCountry,
        MerchantReferences $merchantReferences,
        Options $options,
        CustomerGenerator $customerGenerator,
        Miscellaneous $miscellaneous,
        AddressBuilder $addressBuilder,
        Parameter $parameter
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->merchantUrls = $merchantUrls;
        $this->orderLines = $orderLines;
        $this->orderTaxAmount = $orderTaxAmount;
        $this->orderAmount = $orderAmount;
        $this->purchaseCountry = $purchaseCountry;
        $this->merchantReferences = $merchantReferences;
        $this->options = $options;
        $this->customerGenerator = $customerGenerator;
        $this->miscellaneous = $miscellaneous;
        $this->addressBuilder = $addressBuilder;
        $this->parameter = $parameter;
    }

    /**
     * Generating the create session request
     *
     * @param CartInterface $magentoQuote
     * @param string $authCallbackToken
     * @return RequestInterface
     */
    public function generateCreateSessionRequest(
        CartInterface $magentoQuote,
        string $authCallbackToken
    ): RequestInterface {
        $this->generateCreateUpdateSessionRequest($magentoQuote, $authCallbackToken, self::GENERATE_TYPE_CREATE);
        return $this->requestBuilder->getRequest();
    }

    /**
     * Generating the update session request
     *
     * @param CartInterface $magentoQuote
     * @param string $authCallbackToken
     * @return RequestInterface
     */
    public function generateUpdateSessionRequest(
        CartInterface $magentoQuote,
        string $authCallbackToken
    ): RequestInterface {
        $this->generateCreateUpdateSessionRequest($magentoQuote, $authCallbackToken, self::GENERATE_TYPE_UPDATE);
        return $this->requestBuilder->getRequest();
    }

    /**
     * Generating the request for creating and updating a session
     *
     * @param CartInterface $magentoQuote
     * @param string $authCallbackToken
     * @param string $type
     */
    private function generateCreateUpdateSessionRequest(
        CartInterface $magentoQuote,
        string $authCallbackToken,
        string $type
    ): void {
        $this->requestBuilder->reset();

        $this->parameter->resetOrderLines();
        $this->parameter->getOrderLineProcessor()
            ->processByQuote($this->parameter, $magentoQuote);

        $this->orderAmount->addToRequest($this->requestBuilder, $magentoQuote);
        $this->miscellaneous->addToRequest($this->requestBuilder, $magentoQuote);
        $this->options->addToRequest($this->requestBuilder, $magentoQuote);
        $this->merchantUrls->addToRequest($this->requestBuilder, $authCallbackToken);
        $this->orderLines->addToRequest($this->requestBuilder, $this->parameter, $magentoQuote);
        $this->orderTaxAmount->addToRequest($this->requestBuilder, $this->parameter, $magentoQuote);
        $this->purchaseCountry->addToRequest($this->requestBuilder, $magentoQuote);

        if ($this->customerGenerator->isPrefillAllowed($magentoQuote)) {
            $this->addressBuilder->addToRequest($this->requestBuilder, $magentoQuote);
            $this->requestBuilder->setCustomer($this->customerGenerator->getWithPrefilledData($magentoQuote));
        } else {
            $this->requestBuilder->setCustomer($this->customerGenerator->getBasicData($magentoQuote));
        }

        $requiredAttributes = [
            'purchase_country',
            'purchase_currency',
            'locale',
            'order_amount',
            'order_lines'
        ];

        $validator = $this->requestBuilder->getValidator();
        $validator->isRequiredValueMissing($requiredAttributes, $type);
        $validator->isSumOrderLinesMatchingOrderAmount();
    }

    /**
     * Creating the place order request
     *
     * @param CartInterface $magentoQuote
     * @param string $authCallbackToken
     */
    public function generatePlaceOrderRequest(CartInterface $magentoQuote, string $authCallbackToken): RequestInterface
    {
        $this->requestBuilder->reset();

        $this->parameter->resetOrderLines();
        $this->parameter->getOrderLineProcessor()
            ->processByQuote($this->parameter, $magentoQuote);

        $this->merchantUrls->addToRequest($this->requestBuilder, $authCallbackToken);
        $this->orderLines->addToRequest($this->requestBuilder, $this->parameter, $magentoQuote);
        $this->orderTaxAmount->addToRequest($this->requestBuilder, $this->parameter, $magentoQuote);
        $this->orderAmount->addToRequest($this->requestBuilder, $magentoQuote);
        $this->purchaseCountry->addToRequest($this->requestBuilder, $magentoQuote);
        $this->requestBuilder->setCustomer($this->customerGenerator->getBasicData($magentoQuote));
        $this->miscellaneous->addToRequest($this->requestBuilder, $magentoQuote);
        $this->addressBuilder->addToRequest($this->requestBuilder, $magentoQuote);
        $this->merchantReferences->addToRequest($this->requestBuilder, $magentoQuote);

        $requiredAttributes = [
            'purchase_country',
            'purchase_currency',
            'locale',
            'order_amount',
            'order_lines',
            'merchant_urls',
            'billing_address'
        ];

        if (!$magentoQuote->getIsVirtual()) {
            $requiredAttributes[] = 'shipping_address';
        }

        $validator = $this->requestBuilder->getValidator();
        $validator->isRequiredValueMissing($requiredAttributes, self::GENERATE_TYPE_PLACE);
        $validator->isSumOrderLinesMatchingOrderAmount();

        return $this->requestBuilder->getRequest();
    }
}
