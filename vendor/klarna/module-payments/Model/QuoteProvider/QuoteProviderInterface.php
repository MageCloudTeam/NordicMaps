<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Model\QuoteProvider;

use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
interface QuoteProviderInterface
{
    /**
     * Fetches the Magento Quote
     */
    public function getQuote(): ?CartInterface;
}
