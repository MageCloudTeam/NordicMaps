<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\System\Message;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Model\System\Message\AuthorizationCallbackHealthCheck;

class AuthorizationCallbackHealthCheckTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject[]
     */
    private array $dependencyMocks;
    /**
     * @var AuthorizationCallbackHealthCheck|object
     */
    private AuthorizationCallbackHealthCheck $authorizationCallbackHealthCheck;
    /**
     * @var MockFactory
     */
    private MockFactory $mockFactory;

    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($this->mockFactory);
        $this->authorizationCallbackHealthCheck = $objectFactory->create(AuthorizationCallbackHealthCheck::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();
    }

    /**
     * @dataProvider orderAttemptsDataProvider
     *
     * @param $totalCreateOrderAttempts
     * @param $failedAttempts
     * @param $expectedResult
     * @return void
     */
    public function testIsMoreThanSpecifiedPercentStatus403InLastXDays(
        $totalCreateOrderAttempts,
        $failedAttempts,
        $expectedResult
    ): void {
        $connection = $this->getMockConnection();
        $connection->expects($this->any())
            ->method('fetchOne')
            ->willReturnOnConsecutiveCalls($totalCreateOrderAttempts, $failedAttempts);

        $this->dependencyMocks['resourceConnection']
            ->method('getConnection')
            ->willReturn($connection);

        $this->assertEquals($expectedResult, $this->authorizationCallbackHealthCheck->isDisplayed());
    }

    /**
     * @return void
     */
    public function testSeverityShouldBe1Or2(): void
    {
        $this->assertContains($this->authorizationCallbackHealthCheck->getSeverity(), [1, 2]);
    }

    protected function orderAttemptsDataProvider(): array
    {
        return [
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 30,
                'expectedResult' => false
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 29,
                'expectedResult' => false
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 10,
                'expectedResult' => false
            ],
            [
                // case: division by zero Exception
                'totalCreateOrderAttempts' => 0,
                'failedAttempts' => 10,
                'expectedResult' => false
            ],
            [
                // case: there is no failed attempts
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 0,
                'expectedResult' => false
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 31,
                'expectedResult' => true
            ],
            [
                'totalCreateOrderAttempts' => 100,
                'failedAttempts' => 100,
                'expectedResult' => true
            ],
        ];
    }

    protected function getMockConnection()
    {
        $select = $this->getMockBuilder(\Magento\Framework\DB\Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $select->expects($this->any())
            ->method('from')
            ->willReturn($select);

        $select->expects($this->any())
            ->method('where')
            ->willReturn($select);

        $connection = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('select')
            ->willReturn($select);

        return $connection;
    }
}
