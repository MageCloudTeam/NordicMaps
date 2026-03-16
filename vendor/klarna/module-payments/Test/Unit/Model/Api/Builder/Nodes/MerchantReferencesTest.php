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
use Klarna\Kp\Model\Api\Builder\Nodes\MerchantReferences;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote;
use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Framework\DataObject;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\MerchantReferences
 */
class MerchantReferencesTest extends TestCase
{
    /**
     * @var MerchantReferences
     */
    private MerchantReferences $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;
    /**
     * @var DataObject
     */
    private DataObject $dataObject;

    public function testAddToRequestSettingTheReferencesToTheRequest(): void
    {
        $this->requestBuilder->expects(static::once())
            ->method('setMerchantReferences')
            ->with($this->dataObject);

        $this->dependencyMocks['dataObjectFactory']->method('create')
            ->willReturn($this->dataObject);

        $this->model->addToRequest($this->requestBuilder, $this->quote);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(MerchantReferences::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->quote = $mockFactory->create(Quote::class);
        $this->requestBuilder = $mockFactory->create(Builder::class);
        $this->dataObject = $mockFactory->create(DataObject::class);
    }
}
