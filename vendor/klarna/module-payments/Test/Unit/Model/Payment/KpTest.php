<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Payment;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;

class KpTest extends TestCase
{
    /**
     * @var Kp
     */
    private Kp $kp;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;
    /**
     * @var Quote
     */
    private Quote $magentoQuote;

    public function testIsActiveKpIsEnabled(): void
    {
        $store = $this->mockFactory->create(Store::class);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['apiValidation']->method('isKpEnabled')
            ->willReturn(true);
        static::assertTrue($this->kp->isActive('1'));
    }

    public function testIsActiveKpIsDisabled(): void
    {
        $store = $this->mockFactory->create(Store::class);
        $this->dependencyMocks['storeManager']->method('getStore')
            ->willReturn($store);
        $this->dependencyMocks['apiValidation']->method('isKpEnabled')
            ->willReturn(false);
        static::assertFalse($this->kp->isActive('1'));
    }

    public function testIsAvailableAdapterReturnsFalse(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(false);

        static::assertFalse($this->kp->isAvailable($this->magentoQuote));
    }

    public function testIsAvailableIsKecSessionReturnsTrue(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(true);
        $this->kp->setCode(Kp::ONE_KLARNA_PAYMENT_METHOD_CODE_WITH_PREFIX);

        static::assertTrue($this->kp->isAvailable($this->magentoQuote));
    }

    public function testIsAvailableProviderReturnsTrue(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(true);
        $this->dependencyMocks['paymentMethodProvider']->method('existMethodInAvailableMethodList')
            ->willReturn(true);

        static::assertTrue($this->kp->isAvailable($this->magentoQuote));
    }

    public function testIsAvailableProviderReturnsFalse(): void
    {
        $this->dependencyMocks['adapter']->method('isAvailable')
            ->willReturn(true);
        $this->dependencyMocks['paymentMethodProvider']->method('existMethodInAvailableMethodList')
            ->willReturn(false);

        static::assertFalse($this->kp->isAvailable($this->magentoQuote));
    }

    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);
        $this->kp = $objectFactory->create(Kp::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->magentoQuote = $this->mockFactory->create(Quote::class);
    }
}
