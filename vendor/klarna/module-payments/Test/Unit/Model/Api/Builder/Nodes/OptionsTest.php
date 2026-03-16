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
use Klarna\Kp\Model\Api\Builder\Nodes\Options;
use Klarna\Kp\Model\Api\Request\Builder;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\Miscellaneous
 */
class OptionsTest extends TestCase
{
    /**
     * @var Options
     */
    private Options $model;
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

    public function testAddToRequestSetOptionsToRequest()
    {
        $targetValue = ['any_key' => 'any_value'];
        $this->dependencyMocks['paymentConfiguration']->method('getDesign')
            ->willReturn($targetValue);
        $this->requestBuilder->expects(static::once())
            ->method('setOptions')
            ->with($targetValue);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(Options::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->requestBuilder = $mockFactory->create(Builder::class);

        $store = $mockFactory->create(Store::class);
        $this->quote = $mockFactory->create(Quote::class, ['getBaseCurrencyCode', 'getStore']);
        $this->quote->method('getStore')
            ->willReturn($store);
    }
}
