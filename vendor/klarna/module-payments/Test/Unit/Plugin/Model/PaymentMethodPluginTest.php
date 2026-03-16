<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Plugin\Model;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Plugin\Model\PaymentMethodPlugin;
use Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Address\PaymentMethod;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Model\Quote;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote as MagentoQuote;

/**
 * @coversDefaultClass \Klarna\Kp\Plugin\Model\PaymentMethodPlugin
 */
class PaymentMethodPluginTest extends TestCase
{
    /**
     * @var MockFactory
     */
    private $mockFactory;
    /**
     * @var PaymentMethodPlugin
     */
    private $model;
    /**
     * @var MockObject[]
     */
    private $dependencyMocks;
    /**
     * @var PaymentMethod|MockObject
     */
    private $subject;

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testAfterGenerateFilterTextKlarnaDisabledReturnUnchangedInput(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(false);

        $result = $this->model->afterGenerateFilterText($this->subject, ['input']);
        static::assertSame(['input'], $result);
    }

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testAfterGenerateFilterTextNoPaymentMethodInArgumentReturnsUnchangedInput(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethods')
            ->willReturn(['klarna_x']);

        $result = $this->model->afterGenerateFilterText($this->subject, ['some:prefix:value']);
        static::assertSame(['some:prefix:value'], $result);
    }

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testAfterGenerateFilterTextNonKlarnaPaymentMethodInArgumentReturnsUnchangedInput(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethods')
            ->willReturn(['klarna_x']);

        $result = $this->model->afterGenerateFilterText($this->subject, [
            'quote_address:payment_method:other_payment_provider_method'
        ]);
        static::assertSame(['quote_address:payment_method:other_payment_provider_method'], $result);
    }

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testAfterGenerateFilterTextKlarnaPaymentMethodInArgumentReturnsReplacedInput(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethods')
            ->willReturn(['x']);

        $result = $this->model->afterGenerateFilterText($this->subject, [
            'quote_address:payment_method:x'
        ]);
        static::assertSame([
            'quote_address:payment_method:klarna_kp'
        ], $result);
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);

        $this->model = $objectFactory->create(PaymentMethodPlugin::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->subject = $this->mockFactory->create(DataObject::class);

        $store = $this->mockFactory->create(Store::class);
        $magentoQuote = $this->mockFactory->create(MagentoQuote::class);
        $magentoQuote->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['magentoQuoteRepository']->method('get')
            ->willReturn($magentoQuote);
    }
}
