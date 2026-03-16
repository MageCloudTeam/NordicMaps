<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Model\QuoteProvider;

use Klarna\Kp\Model\QuoteProvider\SessionAwareQuoteProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Klarna\Kp\Model\QuoteProvider\SessionAwareQuoteProvider
 */
class SessionAwareQuoteProviderTest extends TestCase
{
    public function testItReturnsTheQuoteFromTheSession(): void
    {
        $quote   = $this->createMock(CartInterface::class);
        $session = $this->createMock(Session::class);
        $session->method('getQuote')->willReturn($quote);

        $subject = new SessionAwareQuoteProvider($session);
        $result  = $subject->getQuote();

        $this->assertSame($quote, $result);
    }

    /**
     * @dataProvider exceptionListProvider
     */
    public function testItReturnsNullWhenSessionThrowsException(\Throwable $exception): void
    {
        $session = $this->createMock(Session::class);
        $session->method('getQuote')->willThrowException($exception);

        $subject = new SessionAwareQuoteProvider($session);
        $result  = $subject->getQuote();

        $this->assertNull($result);
    }

    public function exceptionListProvider(): array
    {
        return [
            [new NoSuchEntityException()],
            [new LocalizedException(new Phrase('Bam!'))],
        ];
    }
}
