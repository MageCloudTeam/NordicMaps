<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Builder\Nodes\PurchaseCountry;
use Klarna\Kp\Model\Api\Request\Builder;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\PurchaseCountry
 */
class PurchaseCountryTest extends TestCase
{
    /**
     * @var PurchaseCountry
     */
    private PurchaseCountry $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Address
     */
    private Address $billingAddress;
    /**
     * @var Address
     */
    private Address $shippingAddress;

    public function testAddToRequestSettingCountryFromBillingAddress(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('DE');
        $this->requestBuilder->expects(static::once())
            ->method('setPurchaseCountry')
            ->with('DE');

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    public function testAddToRequestSettingCountryFromShippingAddress(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('');
        $this->shippingAddress->method('getCountry')
            ->willReturn('DE');
        $this->requestBuilder->expects(static::once())
            ->method('setPurchaseCountry')
            ->with('DE');

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    public function testAddToRequestSettingDefaultCountry(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('');
        $this->shippingAddress->method('getCountry')
            ->willReturn('');
        $this->dependencyMocks['directoryHelper']->method('getDefaultCountry')
            ->willReturn('DE');
        $this->requestBuilder->expects(static::once())
            ->method('setPurchaseCountry')
            ->with('DE');

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(PurchaseCountry::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->requestBuilder = $mockFactory->create(Builder::class);
        $this->quote = $mockFactory->create(Quote::class);

        $this->billingAddress = $mockFactory->create(Address::class);
        $this->shippingAddress = $mockFactory->create(Address::class);

        $this->quote->method('getBillingAddress')
            ->willReturn($this->billingAddress);
        $this->quote->method('getShippingAddress')
            ->willReturn($this->shippingAddress);

        $store = $mockFactory->create(Store::class);
        $this->quote->method('getStore')
            ->willReturn($store);
    }
}
