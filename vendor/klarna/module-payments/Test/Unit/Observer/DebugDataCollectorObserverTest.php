<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Test\Unit\Observer;

use Magento\Framework\Event;
use PHPUnit\Framework\TestCase;
use Klarna\Base\Helper\Debug\DebugDataObject;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Observer\DebugDataCollectorObserver;
use Magento\Framework\Event\Observer;

/**
 * @internal
 */
class DebugDataCollectorObserverTest extends TestCase
{
    /**
     * @var DebugDataCollectorObserver
     */
    private $debugDataCollectorObserver;

    /**
     * @var MockObject[]
     */
    private array $dependencyMocks;

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->debugDataCollectorObserver = $objectFactory->create(DebugDataCollectorObserver::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();
    }

    public function testExecutionAddsStringifiedTableDataToDebugDataObject(): void
    {
        $debugDataObject = $this->createMock(DebugDataObject::class);
        $debugDataObject->expects($this->once())
            ->method('addData');

        $event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->disableAutoReturnValueGeneration()
            ->addMethods(['getDebugDataObject'])
            ->getMock();

        $event->expects($this->once())
            ->method('getDebugDataObject')
            ->willReturn($debugDataObject);

        $observer = $this->createMock(Observer::class);
        $observer
            ->expects($this->once())
            ->method('getEvent')
            ->willReturn($event);

        $this->dependencyMocks['stringifyDbTableData']
            ->expects($this->once())
            ->method('getStringData')
            ->with(
                $this->equalTo('klarna_payments_quote'),
                $this->equalTo([
                    'orderBy' => 'payments_quote_id DESC',
                    'limit' => 1000,
                ])
            )
            ->willReturn('stringified data');

        $this->debugDataCollectorObserver->execute($observer);
    }
}
