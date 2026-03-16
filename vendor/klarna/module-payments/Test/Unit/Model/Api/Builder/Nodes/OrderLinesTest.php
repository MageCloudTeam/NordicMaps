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
use Klarna\Kp\Model\Api\Builder\Nodes\OrderLines;
use Klarna\Kp\Model\Api\Request\Builder;
use Klarna\Orderlines\Model\Container\Parameter;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\OrderLines
 */
class OrderLinesTest extends TestCase
{
    /**
     * @var OrderLines
     */
    private OrderLines $model;
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
            ->method('addOrderlines');
        self::expectException(KlarnaException::class);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    public function testAddToRequestSettingToRequest(): void
    {
        $target = ['a' => 'b'];
        $this->parameter->method('getOrderLines')
            ->willReturn($target);
        $this->requestBuilder->expects(static::once())
            ->method('addOrderlines')
            ->with($target);

        $this->model->addToRequest($this->requestBuilder, $this->parameter, $this->quote);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(OrderLines::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->requestBuilder = $mockFactory->create(Builder::class);
        $this->parameter = $mockFactory->create(Parameter::class);
        $this->quote = $mockFactory->create(Quote::class);
    }
}
