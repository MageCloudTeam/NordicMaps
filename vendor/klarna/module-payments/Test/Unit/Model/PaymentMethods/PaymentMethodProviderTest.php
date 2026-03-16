<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\PaymentMethods;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Response;
use Klarna\Kp\Model\Payment\Kp;
use Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote as MagentoQuote;
use Klarna\Kp\Model\Quote as KlarnaQuote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider
 */
class PaymentMethodProviderTest extends TestCase
{
    /**
     * @var PaymentMethodProvider
     */
    private PaymentMethodProvider $paymentMethodProvider;
    /**
     * @var Kp
     */
    private Kp $kpInstance;
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var MagentoQuote
     */
    private MagentoQuote $magentoQuote;

    public function testCreatePaymentMethodReturnsCreatedInstance(): void
    {
        static::assertSame($this->kpInstance, $this->paymentMethodProvider->createPaymentMethod('test'));
    }

    public function testCreatePaymentMethodReturnsCachedInstance(): void
    {
        $instanceOne = $this->paymentMethodProvider->createPaymentMethod('test');
        $instanceTwo = $this->paymentMethodProvider->createPaymentMethod('test');
        static::assertSame($instanceOne, $instanceTwo);
    }

    public function testExtractByApiResponseNoPaymentMethodCategoriesGiven(): void
    {
        $apiResponse = $this->mockFactory->create(Response::class);
        $apiResponse->method('getPaymentMethodCategories')
            ->willReturn([]);

        static::assertEquals('', $this->paymentMethodProvider->extractByApiResponse($apiResponse));
    }

    public function testExtractByApiResponsePaymentMethodCategoriesGiven(): void
    {
        $apiResponse = $this->mockFactory->create(Response::class);
        $apiResponse->method('getPaymentMethodCategories')
            ->willReturn(
                [
                    [
                        'identifier' => 'method_a'
                    ],
                    [
                        'identifier' => 'method_b'
                    ]
                ]
            );

        static::assertEquals(
            'klarna_method_a,klarna_method_b',
            $this->paymentMethodProvider->extractByApiResponse($apiResponse)
        );
    }

    public function testGetAvailablePaymentMethodsNoPaymentMethodDataGiven(): void
    {
        $result = $this->paymentMethodProvider->getAvailablePaymentMethods([], []);
        static::assertSame([], $result['payment']['klarna_kp']['available_methods']);
    }

    public function testGetAvailablePaymentMethodsPaymentMethodDataEntryGivenReturnsCorrectPaymentData(): void
    {
        $paymentMethodData = [
            [
                'identifier' => 'the_identifier',
                'name' => 'Payment method name',
                'asset_urls' => [
                    'standard' => 'The url'
                ]
            ]
        ];

        $paymentMethodConfig = [
            'payment' => [
                'klarna_kp' => [

                ]
            ]
        ];

        $result = $this->paymentMethodProvider->getAvailablePaymentMethods($paymentMethodData, $paymentMethodConfig);
        $entry = $result['payment']['klarna_kp']['available_methods'][0];
        static::assertSame('klarna_the_identifier', $entry['type']);
        static::assertSame('Klarna_Kp/js/view/payments/kp', $entry['component']);
    }

    public function testGetAvailablePaymentMethodsPaymentMethodDataEntryGivenUpdatedPaymentConfig(): void
    {
        $paymentMethodData = [
            [
                'identifier' => 'the_identifier',
                'name' => 'Payment method name',
                'asset_urls' => [
                    'standard' => 'The url'
                ]
            ]
        ];

        $paymentMethodConfig = [
            'payment' => [
                'klarna_kp' => [
                    'any_key' => 'any value'
                ]
            ]
        ];

        $result = $this->paymentMethodProvider->getAvailablePaymentMethods($paymentMethodData, $paymentMethodConfig);
        static::assertSame('Payment method name', $result['payment']['klarna_the_identifier']['title']);
        static::assertSame('The url', $result['payment']['klarna_the_identifier']['logo']);
    }

    public function testExistMethodInAvailableMethodListNoActiveKlarnaQuoteFound(): void
    {
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willthrowException(new NoSuchEntityException());

        static::assertFalse($this->paymentMethodProvider->existMethodInAvailableMethodList($this->magentoQuote, ''));
    }

    public function testExistMethodInAvailableMethodListKlarnaQuoteHasNoPaymentMethods(): void
    {
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willReturn($klarnaQuote);
        $klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn([]);

        static::assertFalse($this->paymentMethodProvider->existMethodInAvailableMethodList($this->magentoQuote, ''));
    }

    public function testExistMethodInAvailableMethodListMethodNotPartOfList(): void
    {
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willReturn($klarnaQuote);
        $klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn([
                [
                    'identifier' => 'dfsdfsd'
                ],
                [
                    'identifier' => 'dfssdfsd'
                ],
            ]);

        static::assertFalse(
            $this
                ->paymentMethodProvider
                ->existMethodInAvailableMethodList($this->magentoQuote, 'hfhfhfgh')
        );
    }

    public function testExistMethodInAvailableMethodListMethodPartOfList(): void
    {
        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuote')
            ->willReturn($klarnaQuote);
        $klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn([
                [
                    'identifier' => 'sfsdf'
                ],
                [
                    'identifier' => 'test'
                ],
            ]);

        static::assertTrue(
            $this
                ->paymentMethodProvider
                ->existMethodInAvailableMethodList($this->magentoQuote, 'klarna_test')
        );
    }

    protected function setUp(): void
    {
        $this->mockFactory   = new MockFactory($this);
        $objectFactory       = new TestObjectFactory($this->mockFactory);
        $this->paymentMethodProvider = $objectFactory->create(PaymentMethodProvider::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->kpInstance = $this->mockFactory->create(Kp::class);
        $objectFactory->getDependencyMocks()['methodFactory']->method('create')
            ->willReturn($this->kpInstance);

        $this->magentoQuote = $this->mockFactory->create(MagentoQuote::class);
    }
}
