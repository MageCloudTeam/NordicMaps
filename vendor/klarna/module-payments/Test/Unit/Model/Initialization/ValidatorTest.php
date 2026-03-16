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
use Klarna\Kp\Model\Initialization\Validator;
use Klarna\Kp\Model\Quote;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Model\Quote as MagentoQuote;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @coversDefaultClass \Klarna\Kp\Model\Initialization\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private Validator $validator;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var MagentoQuote
     */
    private MagentoQuote $magentoQuote;
    /**
     * @var Quote
     */
    private Quote $klarnaQuote;

    public function testIsKlarnaSessionRunningNoDatabaseEntryFound(): void
    {
        $this->dependencyMocks['quoteRepository']->method('getActiveByQuote')
            ->willthrowException(new NoSuchEntityException(__('')));

        static::assertFalse($this->validator->isKlarnaSessionRunning($this->magentoQuote));
    }

    public function testIsKlarnaSessionRunningNoSessionIdSet(): void
    {
        $this->dependencyMocks['quoteRepository']->method('getActiveByQuote')
            ->willReturn($this->klarnaQuote);
        $this->dependencyMocks['quoteRepository']->expects(static::once())
            ->method('markInactive');

        static::assertFalse($this->validator->isKlarnaSessionRunning($this->magentoQuote));
    }

    public function testIsKlarnaSessionRunningEntryExistsAndSessionIdSet(): void
    {
        $this->klarnaQuote->method('getSessionId')
            ->willReturn('1');
        $this->dependencyMocks['quoteRepository']->method('getActiveByQuote')
            ->willReturn($this->klarnaQuote);
        $this->dependencyMocks['quoteRepository']->expects(static::never())
            ->method('markInactive');

        static::assertTrue($this->validator->isKlarnaSessionRunning($this->magentoQuote));
    }

    public function testIsKlarnaSessionRunningEntryExistsAndItsKecSession(): void
    {
        $this->klarnaQuote->method('isKecSession')
            ->willReturn(true);
        $this->dependencyMocks['quoteRepository']->method('getActiveByQuote')
            ->willReturn($this->klarnaQuote);
        $this->dependencyMocks['quoteRepository']->expects(static::never())
            ->method('markInactive');

        static::assertTrue($this->validator->isKlarnaSessionRunning($this->magentoQuote));
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->validator = $objectFactory->create(Validator::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->magentoQuote = $mockFactory->create(MagentoQuote::class);
        $this->klarnaQuote = $mockFactory->create(Quote::class);
    }
}
