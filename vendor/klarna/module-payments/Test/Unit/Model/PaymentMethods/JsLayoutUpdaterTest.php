<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\PaymentMethods;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\PaymentMethods\JsLayoutUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\PaymentMethods\JsLayoutUpdater
 */
class JsLayoutUpdaterTest extends TestCase
{
    /**
     * @var JsLayoutUpdater
     */
    private JsLayoutUpdater $jsLayoutUpdater;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var KlarnaQuote
     */
    private KlarnaQuote $klarnaQuote;

    /**
     * @dataProvider jsLayoutDataProvider
     */
    public function testUpdateMethodsSendingRequestNotAllowed(array $initialConfiguration): void
    {
        $this->dependencyMocks['apiValidation']->method('sendApiRequestAllowed')
            ->willReturn(false);

        $result = $this->jsLayoutUpdater->updateMethods($initialConfiguration);
        static::assertSame($initialConfiguration, $result);
    }

    /**
     * @dataProvider jsLayoutDataProvider
     */
    public function testUpdateMethodsNoPaymentMethodInfoGiven(array $initialConfiguration): void
    {
        $this->dependencyMocks['apiValidation']->method('sendApiRequestAllowed')
            ->willReturn(true);
        $this->klarnaQuote->method('getPaymentMethodInfo')->willReturn([]);

        $result = $this->jsLayoutUpdater->updateMethods($initialConfiguration);
        static::assertSame($initialConfiguration, $result);
    }

    /**
     * @dataProvider jsLayoutDataProvider
     */
    public function testUpdateMethodsKpMethodsExistsInDatabase(array $initialConfiguration): void
    {
        $methods = [
            [
                'identifier' => 'any_identifier'
            ],
            [
                'identifier' => 'other_identifier'
            ]
        ];
        $this->klarnaQuote->method('getPaymentMethodInfo')->willReturn($methods);
        $this->dependencyMocks['apiValidation']->method('sendApiRequestAllowed')
            ->willReturn(true);

        $expected = $initialConfiguration;
        $expected['klarna']['methods']['klarna_any_identifier'] = ['identifier' => 'kp_identifier'];
        $expected['klarna']['methods']['klarna_other_identifier'] = ['identifier' => 'kp_identifier'];

        $result = $this->jsLayoutUpdater->updateMethods($initialConfiguration);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider jsLayoutDataProvider
     */
    public function testUpdateMethodsNoMethodShouldChangeOnException(array $initialConfiguration): void
    {
        $this->dependencyMocks['action']->method('sendRequest')
            ->willThrowException(new KlarnaException(__('')));
        $expected = $initialConfiguration;

        $result = $this->jsLayoutUpdater->updateMethods($initialConfiguration);

        static::assertSame($expected, $result);
    }

    public function jsLayoutDataProvider(): array
    {
        return [
            [
                'configuration' =>
                    [
                        'klarna' => [
                            'methods' => [
                                'klarna_kp' => [
                                    'identifier' => 'kp_identifier'
                                ],
                                'klarna_any_identifier' => [
                                    'identifier' => 'klarna_any_identifier'
                                ],
                                'klarna_other_identifier' => [
                                    'identifier' => 'klarna_other_identifier'
                                ]
                            ]
                        ]
                    ]
            ]
        ];
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->jsLayoutUpdater = $objectFactory->create(JsLayoutUpdater::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();
        $this->klarnaQuote = $mockFactory->create(KlarnaQuote::class);

        $this->dependencyMocks['action']->method('sendRequest')
            ->willReturn($this->klarnaQuote);

        $quote = $mockFactory->create(Quote::class);
        $store = $mockFactory->create(Store::class);
        $quote->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['session']->method('getQuote')
            ->willReturn($quote);
    }
}
