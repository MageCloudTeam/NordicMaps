<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Builder\Nodes;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Builder\Nodes\OrderTaxAmount;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Orderlines\Model\Container\Parameter;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\OrderTaxAmount
 */
class OrderTaxAmountTest extends TestCase
{
    /**
     * @var OrderTaxAmount
     */
    private OrderTaxAmount $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var Parameter
     */
    private Parameter $parameter;
    /**
     * @var Quote
     */
    private Quote $quote;

    public function testAddToRequestEmptyOrderLineList(): void
    {
        $this->parameter->method('getOrderLines')
            ->willReturn([]);
        $this->requestBuilder->expects(static::never())
            ->method('setOrderTaxAmount');
        self::expectException(KlarnaException::class);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    public function testAddToRequestSummingTotalTaxAmount(): void
    {
        $orderLines = [
            [
                'type' => 'a',
                'total_tax_amount' => 5
            ],
            [
                'type' => 'a',
                'total_tax_amount' => 7
            ]
        ];
        $this->parameter->method('getOrderLines')
            ->willReturn($orderLines);
        $this->requestBuilder->expects(static::once())
            ->method('setOrderTaxAmount')
            ->with(12);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    public function testAddToRequestFoundSalesTax(): void
    {
        $orderLines = [
            [
                'type' => 'a',
                'total_tax_amount' => 5
            ],
            [
                'type' => 'sales_tax',
                'total_amount' => 7
            ]
        ];
        $this->parameter->method('getOrderLines')
            ->willReturn($orderLines);
        $this->requestBuilder->expects(static::once())
            ->method('setOrderTaxAmount')
            ->with(7);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(OrderTaxAmount::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->requestBuilder = $mockFactory->create(Builder::class);
        $this->parameter = $mockFactory->create(Parameter::class);
        $this->quote = $mockFactory->create(Quote::class);
    }
}
