<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\QuoteProvider;

use Magento\Quote\Api\Data\CartInterface;

/**
 * A facade. Caches the result of previous QuoteProviders.
 *
 * @internal
 */
class CachingQuoteProvider implements QuoteProviderInterface
{
    /**
     * @var QuoteProviderInterface[]
     */
    private $quoteProviders;
    /**
     * @var ?CartInterface
     */
    private $cachedQuote;
    /**
     * @var bool
     */
    private $isFirstCall = true;

    /**
     * @param QuoteProviderInterface[] $quoteProviders
     * @codeCoverageIgnore
     */
    public function __construct(array $quoteProviders)
    {
        $this->quoteProviders = $quoteProviders;
    }

    /**
     * @inheritDoc
     */
    public function getQuote(): ?CartInterface
    {
        if (!$this->isFirstCall) {
            return $this->cachedQuote;
        }

        $this->cachedQuote = $this->getQuoteFromProviders();
        $this->isFirstCall = false;

        return $this->cachedQuote;
    }

    /**
     * Gets the first available Quote from quoteProviders
     */
    private function getQuoteFromProviders(): ?CartInterface
    {
        foreach ($this->quoteProviders as $quoteProvider) {
            $quote = $quoteProvider->getQuote();
            if ($quote instanceof CartInterface) {
                return $quote;
            }
        }

        return null;
    }
}
