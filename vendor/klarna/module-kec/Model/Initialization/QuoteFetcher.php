<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Initialization;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\QuoteFactory;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class QuoteFetcher
{

    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;
    /**
     * @var QuoteFiller
     */
    private QuoteFiller $quoteFiller;

    /**
     * @param Session $session
     * @param QuoteFactory $quoteFactory
     * @param QuoteFiller $quoteFiller
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $session,
        QuoteFactory $quoteFactory,
        QuoteFiller $quoteFiller
    ) {
        $this->session = $session;
        $this->quoteFactory = $quoteFactory;
        $this->quoteFiller = $quoteFiller;
    }

    /**
     * Getting back a Magento quote
     *
     * @param array $parameter
     * @return CartInterface
     */
    public function getMagentoQuote(array $parameter): CartInterface
    {
        if ($parameter['additional_input']['use_existing_quote'] === '1') {
            $magentoQuote = $this->session->getQuote();
            $magentoQuote = $this->quoteFiller->fillExistingMagentoQuote($magentoQuote, $parameter);
        } else {
            $magentoQuote = $this->quoteFactory->create();
            $magentoQuote = $this->quoteFiller->fillEmptyMagentoQuote($magentoQuote, $parameter);
        }

        $magentoQuote->setTotalsCollectedFlag(false);
        $magentoQuote->collectTotals();

        return $magentoQuote;
    }
}
