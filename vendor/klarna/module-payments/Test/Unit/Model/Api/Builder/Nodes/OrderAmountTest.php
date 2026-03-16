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
use Klarna\Kp\Model\Api\Builder\Nodes\OrderAmount;
use Klarna\Kp\Model\Api\Request\Builder;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\OrderAmount
 */
class OrderAmountTest extends TestCase
{
    /**
     * @var OrderAmount
     */
    private OrderAmount $model;
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
    private Address $address;

    public function testAddToRequestQuoteIsVirtual(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(true);
        $this->quote->method('getBillingAddress')
            ->willReturn($this->address);

        $targetValue = 500;
        $this->dependencyMocks['dataConverter']->method('toApiFloat')
            ->willReturn($targetValue);
        $this->requestBuilder->expects(static::once())
            ->method('setOrderAmount')
            ->with($targetValue);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    public function testAddToRequestQuoteIsNotVirtual(): void
    {
        $this->quote->method('isVirtual')
            ->willReturn(false);
        $this->quote->method('getShippingAddress')
            ->willReturn($this->address);

        $targetValue = 500;
        $this->dependencyMocks['dataConverter']->method('toApiFloat')
            ->willReturn($targetValue);
        $this->requestBuilder->expects(static::once())
            ->method('setOrderAmount')
            ->with($targetValue);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(OrderAmount::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->requestBuilder = $mockFactory->create(Builder::class);

        $this->quote = $mockFactory->create(Quote::class);
        $this->address = $mockFactory->create(Address::class);
    }
}
