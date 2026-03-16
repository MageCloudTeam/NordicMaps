<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin\Model\Cart\ShippingMethod;

use Klarna\Kco\Model\Carrier\Klarna;
use Klarna\Kco\Model\Checkout\Kco\Session;
use Klarna\Base\Model\Quote\ShippingMethod\QuoteMethodHandler;
use Klarna\Base\Model\Quote\ShippingMethod\SelectionAssurance;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class SelectionAssurancePlugin
{
    /**
     * @var Session
     */
    private Session $kcoSession;
    /**
     * @var QuoteMethodHandler
     */
    private QuoteMethodHandler $quoteMethodHandler;

    /**
     * @param Session $kcoSession
     * @param QuoteMethodHandler $quoteMethodHandler
     * @codeCoverageIgnore
     */
    public function __construct(Session $kcoSession, QuoteMethodHandler $quoteMethodHandler)
    {
        $this->kcoSession = $kcoSession;
        $this->quoteMethodHandler = $quoteMethodHandler;
    }

    /**
     * Setting the KSS shipping method if KSS is used
     *
     * @param SelectionAssurance $selectionAssurance
     * @param SelectionAssurance $result
     * @param CartInterface $quote
     * @return SelectionAssurance
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetCustomShippingMethod(
        SelectionAssurance $selectionAssurance,
        SelectionAssurance $result,
        CartInterface $quote
    ): SelectionAssurance {
        $this->kcoSession->setQuote($quote);

        if ($this->kcoSession->hasActiveKlarnaShippingGatewayInformation()) {
            $this->quoteMethodHandler->setShippingMethod($quote, Klarna::GATEWAY_KEY);
        }

        return $result;
    }
}
