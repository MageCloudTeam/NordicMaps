<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\Api\Rest\Service;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Api\Response;
use Klarna\Kp\Model\Api\Rest\Service\Payments;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Klarna\Base\Config\ApiVersion;
use Klarna\Kp\Model\Api\Request;

/**
 * @coversDefaultClass  \Klarna\Kp\Model\Api\Rest\Service\Payments
 */
class PaymentsTest extends TestCase
{
    /**
     * @var MockFactory
     */
    private $mockFactory;
    /**
     * @var Payments
     */
    private $model;
    /**
     * @var MockObject[]
     */
    private $dependencyMocks;

    /**
     * @covers ::createSession()
     * @doesNotPerformAssertions
     */
    public function testCreateSessionIsUserAgentSetCorrectly(): void
    {
        $version = $this->createMock(ApiVersion::class);

        $this->dependencyMocks['klarnaConfig']
            ->method('getVersionConfig')
            ->willReturn($version);

        $this->dependencyMocks['versionInfo']
            ->method('getVersion')
            ->willReturn('');

        $this->dependencyMocks['versionInfo']
            ->method('getModuleVersionString')
            ->willReturn('module_version_string');

        $this->dependencyMocks['versionInfo']
            ->method('getFullM2KlarnaVersion')
            ->willReturn('m2-klarna/e.f.g');

        $this->dependencyMocks['versionInfo']
            ->method('getMageInfo')
            ->willReturn('Magento mage_edition/mage_version mage_mode mode');

        $this->dependencyMocks['service']
            ->method('makeRequest')
            ->willReturn([
                'response_status_code' => 123
            ]);

        $response = $this->createMock(Response::class);
        $this->dependencyMocks['responseFactory']
            ->method('create')
            ->willReturn($response);

        $request = $this->mockFactory->create(Request::class);
        $request->method('toArray')->willReturn([]);

        $this->dependencyMocks['service']
            ->method('setUserAgent')
            ->with(
                'Magento2_KP',
                'module_version_string;m2-klarna/e.f.g',
                'Magento mage_edition/mage_version mage_mode mode'
            );

        $this->model->createSession($request);
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);

        $this->model = $objectFactory->create(Payments::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();
    }
}
