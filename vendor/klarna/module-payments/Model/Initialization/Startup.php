<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Initialization;

use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\QuoteInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Startup
{
    /**
     * @var KlarnaQuote
     */
    private KlarnaQuote $klarnaQuote;

    /**
     * @param KlarnaQuote $klarnaQuote
     * @codeCoverageIgnore
     */
    public function __construct(KlarnaQuote $klarnaQuote)
    {
        $this->klarnaQuote = $klarnaQuote;
    }

    /**
     * Creating the Klarna session
     *
     * @param CartInterface $quote
     * @return QuoteInterface
     * @throws KlarnaApiException
     * @throws \Klarna\Base\Exception
     */
    public function createKlarnaSession(CartInterface $quote): QuoteInterface
    {
        return $this->klarnaQuote->createNewKlarnaQuote($quote);
    }
}
