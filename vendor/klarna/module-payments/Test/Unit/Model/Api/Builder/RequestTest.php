<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder;

use Klarna\Base\Model\Api\OrderLineProcessor;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Builder\Request;
use Klarna\Kp\Model\Api\Request\Validator;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Request
 */
class RequestTest extends TestCase
{

    /**
     * @var Request
     */
    private Request $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var OrderLineProcessor
     */
    private OrderLineProcessor $orderLineProcessor;
    /**
     * @var Validator
     */
    private Validator $validator;

    public function testGenerateCreateSessionRequestCalledOrderLinesProcessor(): void
    {
        $this->orderLineProcessor->expects(static::once())
            ->method('processByQuote');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledMerchantUrlsLogic():void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledOrderLinesLogic(): void
    {
        $this->dependencyMocks['orderLines']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledOrderTaxAmountLogic(): void
    {
        $this->dependencyMocks['orderTaxAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledOrderAmountLogic(): void
    {
        $this->dependencyMocks['orderAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledPurchaseCountryLogic(): void
    {
        $this->dependencyMocks['purchaseCountry']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledMiscellaneousLogic(): void
    {
        $this->dependencyMocks['miscellaneous']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestCalledOptionsLogic(): void
    {
        $this->dependencyMocks['options']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestNotAddingAddressBecausePrefillIsNotAllowed(): void
    {
        $this->dependencyMocks['customerGenerator']->expects(static::once())
            ->method('isPrefillAllowed')
            ->willReturn(false);

        $this->dependencyMocks['addressBuilder']->expects(static::never())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionRequestAddingAddressBecausePrefillIsAllowed(): void
    {
        $this->dependencyMocks['customerGenerator']->expects(static::once())
            ->method('isPrefillAllowed')
            ->willReturn(true);

        $this->dependencyMocks['addressBuilder']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateCreateSessionCalledValidatorLogic(): void
    {
        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with([
                'purchase_country',
                'purchase_currency',
                'locale',
                'order_amount',
                'order_lines'
            ]);
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generateCreateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderLinesProcessor(): void
    {
        $this->orderLineProcessor->expects(static::once())
            ->method('processByQuote');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledMerchantUrlsLogic():void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderLinesLogic(): void
    {
        $this->dependencyMocks['orderLines']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderTaxAmountLogic(): void
    {
        $this->dependencyMocks['orderTaxAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOrderAmountLogic(): void
    {
        $this->dependencyMocks['orderAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledPurchaseCountryLogic(): void
    {
        $this->dependencyMocks['purchaseCountry']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledMiscellaneousLogic(): void
    {
        $this->dependencyMocks['miscellaneous']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestCalledOptionsLogic(): void
    {
        $this->dependencyMocks['options']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestNotAddingAddressBecausePrefillIsNotAllowed(): void
    {
        $this->dependencyMocks['customerGenerator']->method('isPrefillAllowed')
            ->willReturn(false);

        $this->dependencyMocks['addressBuilder']->expects(static::never())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionRequestAddingAddressBecausePrefillIsAllowed(): void
    {
        $this->dependencyMocks['customerGenerator']->method('isPrefillAllowed')
            ->willReturn(true);

        $this->dependencyMocks['addressBuilder']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGenerateUpdateSessionCalledValidatorLogic(): void
    {
        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with([
                'purchase_country',
                'purchase_currency',
                'locale',
                'order_amount',
                'order_lines'
            ]);
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generateUpdateSessionRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderLinesProcessor(): void
    {
        $this->orderLineProcessor->expects(static::once())
            ->method('processByQuote');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledMerchantUrlsLogic():void
    {
        $this->dependencyMocks['merchantUrls']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderLinesLogic(): void
    {
        $this->dependencyMocks['orderLines']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderTaxAmountLogic(): void
    {
        $this->dependencyMocks['orderTaxAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledOrderAmountLogic(): void
    {
        $this->dependencyMocks['orderAmount']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledPurchaseCountryLogic(): void
    {
        $this->dependencyMocks['purchaseCountry']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledCustomerLogic(): void
    {
        $basicData = ['a' => 'b'];
        $this->dependencyMocks['customerGenerator']->expects(static::once())
            ->method('getBasicData')
            ->willReturn($basicData);
        $this->dependencyMocks['requestBuilder']->expects(static::once())
            ->method('setCustomer')
            ->with($basicData);

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledMiscellaneousLogic(): void
    {
        $this->dependencyMocks['miscellaneous']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledAddressLogic(): void
    {
        $this->dependencyMocks['addressBuilder']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestCalledMerchantReferencesLogic(): void
    {
        $this->dependencyMocks['merchantReferences']->expects(static::once())
            ->method('addToRequest');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestQuoteIsVirtualAndShippingAddressWillNotBeChecked(): void
    {
        $this->quote->method('getIsVirtual')
            ->willReturn(true);

        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with([
                'purchase_country',
                'purchase_currency',
                'locale',
                'order_amount',
                'order_lines',
                'merchant_urls',
                'billing_address'
            ]);
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this
            ->model
            ->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    public function testGeneratePlaceOrderRequestQuoteIsNotVirtualAndShippingAddressWillBeChecked(): void
    {
        $this->quote->method('getIsVirtual')
            ->willReturn(false);

        $this->validator->expects(static::once())
            ->method('isRequiredValueMissing')
            ->with(
                [
                'purchase_country',
                'purchase_currency',
                'locale',
                'order_amount',
                'order_lines',
                'merchant_urls',
                'billing_address',
                'shipping_address'
                ],
                Request::GENERATE_TYPE_PLACE
            );
        $this->validator->expects(static::once())
            ->method('isSumOrderLinesMatchingOrderAmount');

        $this->model->generatePlaceOrderRequest($this->quote, 'a-random-auth-callback-token');
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(Request::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->quote = $mockFactory->create(Quote::class);
        $this->orderLineProcessor = $mockFactory->create(OrderLineProcessor::class);
        $this->dependencyMocks['parameter']->method('getOrderLineProcessor')
            ->willReturn($this->orderLineProcessor);

        $request = $mockFactory->create(\Klarna\Kp\Model\Api\Request::class);
        $this->dependencyMocks['requestBuilder']->method('getRequest')
            ->willReturn($request);

        $this->validator = $mockFactory->create(Validator::class);
        $this->dependencyMocks['requestBuilder']->method('getValidator')
            ->willReturn($this->validator);
    }
}
