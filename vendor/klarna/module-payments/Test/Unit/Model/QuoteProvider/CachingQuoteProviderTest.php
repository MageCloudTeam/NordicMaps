<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Test\Unit\Plugin\Payment\Helper;

use Klarna\Kp\Model\QuoteProvider\CachingQuoteProvider;
use Klarna\Kp\Model\QuoteProvider\QuoteProviderInterface;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Klarna\Kp\Model\QuoteProvider\CachingQuoteProvider
 */
class CachingQuoteProviderTest extends TestCase
{
    public function testItCachesTheFirstAvailableQuote(): void
    {
        $quoteMock      = $this->createMock(CartInterface::class);
        $quoteProvider1 = $this->createMock(QuoteProviderInterface::class);
        $quoteProvider1
            ->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $quoteProvider2 = $this->createMock(QuoteProviderInterface::class);
        $quoteProvider2
            ->expects($this->never()) // << never called as the first provider returns Quote, which is cached
            ->method('getQuote');

        $subject = new CachingQuoteProvider([$quoteProvider1, $quoteProvider2]);

        $result1 = $subject->getQuote();
        $this->assertInstanceOf(CartInterface::class, $result1);

        $result2 = $subject->getQuote();
        $this->assertSame($result1, $result2);

        $result3 = $subject->getQuote();
        $this->assertSame($result3, $quoteMock);
    }

    public function testGetQuoteShouldCacheNullToo(): void
    {
        $quoteProvider1 = $this->createMock(QuoteProviderInterface::class);
        $quoteProvider1->expects($this->once())->method('getQuote');
        $quoteProvider2 = $this->createMock(QuoteProviderInterface::class);
        $quoteProvider2->expects($this->once())->method('getQuote');

        $subject = new CachingQuoteProvider([$quoteProvider1, $quoteProvider2]);

        $result1 = $subject->getQuote();
        $this->assertNull($result1);

        $result2 = $subject->getQuote();
        $this->assertNull($result2);

        $result3 = $subject->getQuote();
        $this->assertNull($result3);
    }
}
