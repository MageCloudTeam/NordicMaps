<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes\Addresses;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Builder;
use Klarna\Kp\Model\Payment\Kp;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Model\Api\Request\Builder as RequestBuilder;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\Addresses\Builder
 */
class BuilderTest extends TestCase
{
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;
    /**
     * @var Builder
     */
    private Builder $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var RequestBuilder
     */
    private RequestBuilder $requestBuilder;

    public function testAddToRequestQuoteIsVirtualAndJustBillingAddressIsSetForTheRequest(): void
    {
        $this->quote->method('getIsVirtual')
            ->willReturn(true);
        $this->requestBuilder->expects(static::once())
            ->method('setBillingAddress');
        $this->requestBuilder->expects(static::never())
            ->method('setShippingAddress');

        $this->dependencyMocks['mapper']->method('getKlarnaDataFromAddress')
            ->willReturn([]);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    public function testAddToRequestQuoteIsNotVirtualAndBothAddressesAreSetForTheRequest(): void
    {
        $this->quote->method('getIsVirtual')
            ->willReturn(false);
        $this->requestBuilder->expects(static::once())
            ->method('setBillingAddress');
        $this->requestBuilder->expects(static::once())
            ->method('setShippingAddress');

        $this->dependencyMocks['mapper']->method('getKlarnaDataFromAddress')
            ->willReturn([]);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);

        $this->model = $objectFactory->create(Builder::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $store = $this->mockFactory->create(Store::class);
        $address = $this->mockFactory->create(Address::class);
        $this->quote = $this->mockFactory->create(
            Quote::class,
            [
                'getIsVirtual',
                'getBillingAddress',
                'getShippingAddress',
                'getStore'
            ]
        );
        $this->quote->method('getStore')
            ->willReturn($store);
        $this->quote->method('getBillingAddress')
            ->willReturn($address);
        $this->quote->method('getShippingAddress')
            ->willReturn($address);
        $this->requestBuilder = $this->mockFactory->create(RequestBuilder::class);
    }
}
