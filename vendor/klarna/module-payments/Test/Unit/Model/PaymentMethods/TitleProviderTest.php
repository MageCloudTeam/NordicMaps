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
use Klarna\Kp\Model\PaymentMethods\TitleProvider;
use Klarna\Kp\Model\Quote;
use PHPUnit\Framework\TestCase;
use Magento\Payment\Model\Info;

/**
 * @coversDefaultClass \Klarna\Kp\Model\PaymentMethods\TitleProvider
 */
class TitleProviderTest extends TestCase
{
    /**
     * @var TitleProvider
     */
    private TitleProvider $titleProvider;
    /**
     * @var Info
     */
    private Info $paymentInfo;
    /**
     * @var Quote
     */
    private Quote $klarnaQuote;

    public function testGetByAdditionalInformationReturnAdditionalInformation(): void
    {
        $expected = 'additional information';
        $this->paymentInfo->method('getAdditionalInformation')
            ->with('method_title')
            ->willReturn($expected);

        static::assertEquals(
            'Klarna ' . $expected,
            $this->titleProvider->getByAdditionalInformation($this->paymentInfo)
        );
    }

    public function testGetByAdditionalInformationReturnContentOfHasAdditionalInformation(): void
    {
        $expected = 'additional information';
        $this->paymentInfo->method('hasAdditionalInformation')
            ->with('method_code')
            ->willReturn($expected);

        static::assertEquals(
            'Klarna Payments (' . $expected . ')',
            $this->titleProvider->getByAdditionalInformation($this->paymentInfo)
        );
    }

    public function testGetByAdditionalInformationReturnDefaultValue(): void
    {
        static::assertEquals(
            TitleProvider::DEFAULT_TITLE,
            $this->titleProvider->getByAdditionalInformation($this->paymentInfo)
        );
    }

    public function testGetByKlarnaQuotePaymentMethodsGiven(): void
    {
        $paymentMethodInfo = [
            [
                'identifier' => 'pay_over_time',
                'name' => 'Pay in instalments',
                'asset_urls' => [
                    'descriptive' => 'my descriptive url',
                    'standard' => 'my standard url'
                ]
            ]
        ];
        $this->klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn($paymentMethodInfo);

        $result = $this->titleProvider->getByKlarnaQuote($this->klarnaQuote, 'pay_over_time');
        static::assertEquals($paymentMethodInfo[0]['name'], $result);
    }

    public function testGetByKlarnaQuoteNoPaymentMethodsGiven(): void
    {
        $this->klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn([]);

        $result = $this->titleProvider->getByKlarnaQuote($this->klarnaQuote, 'Choose_your_way_to_pay');
        static::assertEquals(TitleProvider::DEFAULT_TITLE, $result);
    }

    protected function setUp(): void
    {
        $mockFactory   = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->titleProvider = $objectFactory->create(TitleProvider::class);

        $this->paymentInfo = $mockFactory->create(Info::class);
        $this->klarnaQuote = $mockFactory->create(Quote::class);
    }
}
