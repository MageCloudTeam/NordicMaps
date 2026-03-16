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
use Klarna\Kp\Model\Api\Builder\Nodes\MerchantUrls;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Model\Api\Request\Builder;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Api\Builder\Nodes\MerchantUrls
 */
class MerchantUrlsTest extends TestCase
{

    /**
     * @var MerchantUrls
     */
    private MerchantUrls $model;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Builder
     */
    private Builder $requestBuilder;

    public function testAddToRequestSetsUrlsToRequest(): void
    {
        $targetUrl = 'my_url';
        $this->dependencyMocks['url']->method('getDirectUrl')
            ->willReturn($targetUrl);

        $expected = [
            'confirmation' => $targetUrl,
            'notification' => $targetUrl,
            'authorization' => $targetUrl
        ];

        $this->requestBuilder->expects(static::once())
            ->method('setMerchantUrls')
            ->with($expected);

        $this->model->addToRequest($this->requestBuilder, 'a-random-auth-callback-token');
    }

    public function testAddToRequestSetsAuthorizationCallbackToken(): void
    {
        $this->dependencyMocks['url']->method('getDirectUrl')
            ->willReturn('my_url');

        $this->model->addToRequest($this->requestBuilder, 'a-random-auth-callback-token');
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->model = $objectFactory->create(MerchantUrls::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->requestBuilder = $mockFactory->create(Builder::class);
    }
}
