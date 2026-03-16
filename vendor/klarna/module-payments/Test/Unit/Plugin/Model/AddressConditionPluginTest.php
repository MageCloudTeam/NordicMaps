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
use Klarna\Kp\Plugin\Model\AddressConditionPlugin;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Model\Rule\Condition\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Model\Quote;
use Magento\Store\Model\Store;
use Magento\Quote\Model\Quote as MagentoQuote;

/**
 * @coversDefaultClass \Klarna\Kp\Plugin\Model\AddressConditionPlugin
 */
class AddressConditionPluginTest extends TestCase
{
    /**
     * @var MockFactory
     */
    private $mockFactory;
    /**
     * @var AddressConditionPlugin
     */
    private $model;
    /**
     * @var MockObject[]
     */
    private $dependencyMocks;
    /**
     * @var Address|MockObject
     */
    private $subject;

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testBeforeValidateAttributeKlarnaDisabledReturnsUnchangedInput(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(false);

        $result = $this->model->beforeValidateAttribute($this->subject, "input");
        static::assertSame("input", $result);
    }

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testBeforeValidateAttributeUnmatchedPaymentMethodsReturnsUnchangedInput(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethodInformation')
            ->willReturn([['klarna_x']]);

        $result = $this->model->beforeValidateAttribute($this->subject, "input");
        static::assertSame("input", $result);
    }

    /**
     * @covers ::beforeValidateAttribute()
     */
    public function testBeforeValidateAttributeReturnsReplacedInput(): void
    {
        $this->dependencyMocks['apiValidation']
            ->method('isKpEnabled')
            ->willReturn(true);

        $this->dependencyMocks['klarnaSession']->method('getPaymentMethodInformation')
            ->willReturn([['x']]);

        $result = $this->model->beforeValidateAttribute($this->subject, "klarna_x");
        static::assertSame("klarna_kp", $result);
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);

        $this->model = $objectFactory->create(AddressConditionPlugin::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->subject = $this->mockFactory->create(Address::class);

        $store = $this->mockFactory->create(Store::class);
        $magentoQuote = $this->mockFactory->create(MagentoQuote::class);
        $magentoQuote->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['magentoQuoteRepository']->method('get')
            ->willReturn($magentoQuote);
    }
}
