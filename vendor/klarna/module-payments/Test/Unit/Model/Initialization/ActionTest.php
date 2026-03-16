<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\PaymentMethods;

use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Klarna\Kp\Model\Initialization\Action;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote as MagentoQuote;
use Klarna\Kp\Model\Quote;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\Action
 */
class ActionTest extends TestCase
{
    /**
     * @var Action
     */
    private Action $action;
    /**
     * @var array|\PHPUnit\Framework\MockObject\MockObject[]
     */
    private array $dependencyMocks;
    /**
     * @var MagentoQuote
     */
    private MagentoQuote $magentoQuote;
    /**
     * @var Quote|\PHPUnit\Framework\MockObject\MockObject
     */
    private Quote $klarnaQuote;

    public function testSendRequestNoKlarnaSessionExists(): void
    {
        $this->dependencyMocks['startup']->method('createKlarnaSession')
            ->willReturn($this->klarnaQuote);

        $result = $this->action->sendRequest($this->magentoQuote);
        static::assertEquals($this->klarnaQuote, $result);
    }

    public function testSendRequestUpdatingTheSession(): void
    {
        $this->dependencyMocks['validator']->method('isKlarnaSessionRunning')
            ->willReturn(true);
        $this->dependencyMocks['update']->method('updateKlarnaSession')
            ->willReturn($this->klarnaQuote);
        $this->dependencyMocks['validator']->method('getKlarnaQuote')
            ->willReturn($this->klarnaQuote);

        $result = $this->action->sendRequest($this->magentoQuote);
        static::assertEquals($this->klarnaQuote, $result);
    }

    public function testSendRequestJustOneUpdateRequestSent(): void
    {
        $this->dependencyMocks['validator']->method('isKlarnaSessionRunning')
            ->willReturn(true);
        $this->dependencyMocks['update']->expects(static::once())
            ->method('updateKlarnaSession')
            ->willReturn($this->klarnaQuote);
        $this->dependencyMocks['validator']->method('getKlarnaQuote')
            ->willReturn($this->klarnaQuote);

        $this->action->sendRequest($this->magentoQuote);
        $result = $this->action->sendRequest($this->magentoQuote);
        static::assertEquals($this->klarnaQuote, $result);
    }

    public function testSendRequestForcingSendingUpdateRequest(): void
    {
        $this->dependencyMocks['validator']->method('isKlarnaSessionRunning')
            ->willReturn(true);
        $this->dependencyMocks['update']->expects(static::exactly(2))
            ->method('updateKlarnaSession')
            ->willReturn($this->klarnaQuote);
        $this->dependencyMocks['validator']->method('getKlarnaQuote')
            ->willReturn($this->klarnaQuote);

        $this->action->sendRequest($this->magentoQuote);
        $result = $this->action->sendRequest($this->magentoQuote, true);
        static::assertEquals($this->klarnaQuote, $result);
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->action = $objectFactory->create(Action::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->magentoQuote = $mockFactory->create(MagentoQuote::class);
        $this->klarnaQuote = $mockFactory->create(Quote::class);
        $this->klarnaQuote->method('getSessionId')
            ->willReturn('1');
    }
}
