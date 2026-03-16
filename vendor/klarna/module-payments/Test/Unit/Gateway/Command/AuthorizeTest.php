<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Gateway\Command;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Magento\Payment\Gateway\Command\CommandException;
use PHPUnit\Framework\TestCase;
use Klarna\Kp\Gateway\Command\Authorize;

/**
 * @coversDefaultClass \Klarna\Kp\Gateway\Handler\TitleHandler
 */
class AuthorizeTest extends TestCase
{
    /**
     * @var TitleHandler
     */
    private $authorizeCommand;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|(\stdClass&\PHPUnit\Framework\MockObject\MockObject)
     */
    private $mockStdClass;

    /**
     * @dataProvider multipleThrowableProvider
     * @param \Throwable $throwable
     * @return void
     */
    public function testCommandCanHandleAnyTypeOfThrowableAndIsNotLimited(\Throwable $throwable): void
    {
        $this->mockStdClass
            ->method('getPayment')
            ->willThrowException($throwable);

        $this->expectException(CommandException::class);

        $this->authorizeCommand->execute([
            'payment' => $this->mockStdClass
        ]);
    }

    /**
     * This function provides different types of Throwable
     * @return array
     */
    public function multipleThrowableProvider(): array
    {
        return [
            [
                'throwable' => $this->createMock(\Exception::class),
            ],
            [
                'throwable' => $this->createMock(\TypeError::class),
            ],
            [
                'throwable' => $this->createMock(\Error::class),
            ],
            [
                'throwable' => $this->createMock(\RuntimeException::class),
            ],
        ];
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);
        $this->authorizeCommand = $objectFactory->create(Authorize::class);

        $this->mockStdClass = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getPayment'])
            ->getMock();
    }
}
