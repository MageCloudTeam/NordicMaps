<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;
use Klarna\Kp\Model\Api\Response;
use Klarna\Kp\Model\Quote as KlarnaQuote;
use Klarna\Base\Exception as KlarnaException;

class KpConfigProviderTest extends TestCase
{
    /**
     * @var KpConfigProvider
     */
    private KpConfigProvider $kpConfigProvider;
    /**
     * @var array|MockObject[]
     */
    private array $dependencyMocks;
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;

    public function testGetConfigKpNotEnabled(): void
    {
        $this->dependencyMocks['apiValidation']->method('getFailedValidationHistory')
            ->willReturn(['aaa', 'bbb']);

        $result = $this->kpConfigProvider->getConfig();
        static::assertEquals(
            'Klarna Payments will not show up. Reason: aaa, bbb',
            $result['payment']['klarna_kp']['message']
        );
    }

    public function testGetConfigApiRequestReturnedError(): void
    {
        $expected = 'Any error message';

        $this->dependencyMocks['apiValidation']->method('sendApiRequestAllowed')
            ->willReturn(true);

        $this->dependencyMocks['action']->method('sendRequest')
            ->willThrowException(new KlarnaException(__($expected)));

        $result = $this->kpConfigProvider->getConfig();
        static::assertEquals($expected, $result['payment']['klarna_kp']['message']);
    }

    public function testGetConfigReturningConfiguration(): void
    {
        $expected = [
            'any_key' => 'Any value',
            [
                'another_any_key' => 'Another any value'
            ]
        ];

        $this->dependencyMocks['apiValidation']->method('sendApiRequestAllowed')
            ->willReturn(true);

        $response = $this->mockFactory->create(KlarnaQuote::class);
        $response->method('getPaymentMethodInfo')
            ->willReturn(['klarna_pay_later']);
        $this->dependencyMocks['action']->method('sendRequest')
            ->willReturn($response);

        $klarnaQuote = $this->mockFactory->create(KlarnaQuote::class);
        $klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn([]);

        $this->dependencyMocks['paymentMethodProvider']->method('getAvailablePaymentMethods')
            ->willReturn($expected);

        $result = $this->kpConfigProvider->getConfig();
        static::assertEquals($expected, $result);
    }

    protected function setUp(): void
    {
        $this->mockFactory   = new MockFactory($this);
        $objectFactory       = new TestObjectFactory($this->mockFactory);
        $this->kpConfigProvider = $objectFactory->create(KpConfigProvider::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $store = $this->mockFactory->create(Store::class);
        $quote = $this->mockFactory->create(Quote::class);
        $quote->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['session']->method('getQuote')
            ->willReturn($quote);
    }
}
