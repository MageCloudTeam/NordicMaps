<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\Configuration;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use PHPUnit\Framework\TestCase;
use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Configuration\Payment
 */
class PaymentTest extends TestCase
{

    /**
     * @var Payment
     */
    private Payment $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Store
     */
    private Store $store;

    public function testGetAllowedCountriesNoExplicitCountryWasSelected(): void
    {
        $this->dependencyMocks['scopeConfig']->method('getValue')
            ->with('payment/klarna_kp/specificcountry', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn('');

        static::assertEmpty($this->model->getAllowedCountries($this->store));
    }

    public function testGetAllowedCountriesExplicitCountriesWereSelected(): void
    {
        $this->dependencyMocks['scopeConfig']->method('getValue')
            ->with('payment/klarna_kp/specificcountry', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn('DE,AT');

        static::assertEquals(['DE', 'AT'], $this->model->getAllowedCountries($this->store));
    }

    public function testIsB2bEnabledReturnsValue(): void
    {
        $this->dependencyMocks['scopeConfig']->method('isSetFlag')
            ->with('payment/klarna_kp/enable_b2b', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn('1');

        static::assertTrue($this->model->isB2bEnabled($this->store));
    }

    public function testIsDataSharingOnLoadEnabledReturnsValue(): void
    {
        $this->dependencyMocks['scopeConfig']->method('isSetFlag')
            ->with('payment/klarna_kp/data_sharing_onload', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn('1');

        static::assertTrue($this->model->isDataSharingOnLoadEnabled($this->store));
    }

    public function testIsDataSharingEnabledReturnsValue(): void
    {
        $this->dependencyMocks['scopeConfig']->method('isSetFlag')
            ->with('payment/klarna_kp/data_sharing', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn('1');

        static::assertTrue($this->model->isDataSharingEnabled($this->store));
    }

    public function testIsKpEnabledReturnsValue(): void
    {
        $this->dependencyMocks['scopeConfig']->method('isSetFlag')
            ->with('payment/klarna_kp/active', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn('1');

        static::assertTrue($this->model->isKpEnabled($this->store));
    }

    public function testGetDesignNoDesignWasConfigured(): void
    {
        $this->dependencyMocks['scopeConfig']->method('getValue')
            ->with('checkout/klarna_kp_design', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn('');

        static::assertEmpty($this->model->getDesign($this->store));
    }

    public function testGetDesignReturnsConfiguredDesign(): void
    {
        $expected = ['a', 'b'];
        $this->dependencyMocks['scopeConfig']->method('getValue')
            ->with('checkout/klarna_kp_design', ScopeInterface::SCOPE_STORES, $this->store)
            ->willReturn($expected);

        static::assertEquals($expected, $this->model->getDesign($this->store));
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);
        $this->model = $objectFactory->create(Payment::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->store = $mockFactory->create(Store::class);
    }
}
