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
use Klarna\Kp\Model\Api\Builder\Nodes\Miscellaneous;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;
use Klarna\Kp\Model\Api\Request\Builder;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\Miscellaneous
 */
class MiscellaneousTest extends TestCase
{
    /**
     * @var Miscellaneous
     */
    private Miscellaneous $model;
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

    public function testAddToRequestSettingPurchaseCurrency(): void
    {
        $targetCurrency = 'EUR';
        $this->quote->method('getBaseCurrencyCode')
            ->willReturn($targetCurrency);
        $this->requestBuilder->expects(static::once())
            ->method('setPurchaseCurrency')
            ->with($targetCurrency);
        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    public function testAddToRequestSettingLocale(): void
    {
        $this->dependencyMocks['magentoToKlarnaLocaleMapper']->method('getLocale')
            ->willReturn('de_DE');
        $this->requestBuilder->expects(static::once())
            ->method('setLocale')
            ->with('de_DE');
        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(Miscellaneous::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->requestBuilder = $mockFactory->create(Builder::class);
        $this->quote = $mockFactory->create(Quote::class, ['getBaseCurrencyCode']);
    }
}
