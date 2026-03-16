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
use Klarna\Kp\Model\PaymentMethods\Session as KlarnaSession;
use Klarna\Kp\Model\Quote as KpQuote;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @coversDefaultClass \Klarna\Kp\Model\PaymentMethods\Session
 */
class SessionTest extends TestCase
{
    /**
     * @var KlarnaSession
     */
    private KlarnaSession $klarnaSession;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var KpQuote
     */
    private KpQuote $klarnaQuote;

    public function testGetPaymentMethodsNoQuoteIdLinkedtoSession(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn(null);

        static::assertEmpty($this->klarnaSession->getPaymentMethods());
    }

    public function testGetPaymentMethodsNoKlarnaQuoteFound(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuoteId')
            ->willThrowException(new NoSuchEntityException(__('')));

        static::assertEmpty($this->klarnaSession->getPaymentMethods());
    }

    public function testGetPaymentMethodsReturnsPaymentMethods(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuoteId')
            ->willReturn($this->klarnaQuote);

        static::assertEquals($this->klarnaQuote->getPaymentMethods(), $this->klarnaSession->getPaymentMethods());
    }

    public function testGetPaymentMethodInformationNoQuoteIdLinkedtoSession(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn(null);

        static::assertEmpty($this->klarnaSession->getPaymentMethodInformation());
    }

    public function testGetPaymentMethodInformationNoKlarnaQuoteFound(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuoteId')
            ->willThrowException(new NoSuchEntityException(__('')));

        static::assertEmpty($this->klarnaSession->getPaymentMethodInformation());
    }

    public function testGetPaymentMethodInformationReturnsPaymentMethods(): void
    {
        $this->dependencyMocks['checkoutSession']->method('getQuoteId')
            ->willReturn('1');
        $this->dependencyMocks['klarnaQuoteRepository']->method('getActiveByQuoteId')
            ->willReturn($this->klarnaQuote);

        static::assertEquals(
            $this->klarnaQuote->getPaymentMethodInfo(),
            $this->klarnaSession->getPaymentMethodInformation()
        );
    }

    protected function setUp(): void
    {
        $mockFactory   = new MockFactory($this);
        $objectFactory = new TestObjectFactory($mockFactory);

        $this->klarnaSession = $objectFactory->create(KlarnaSession::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->klarnaQuote = $mockFactory->create(KpQuote::class);
        $this->klarnaQuote->method('getPaymentMethods')
            ->willReturn(['a', 'b']);
        $this->klarnaQuote->method('getPaymentMethodInfo')
            ->willReturn(['a', 'b']);
    }
}
