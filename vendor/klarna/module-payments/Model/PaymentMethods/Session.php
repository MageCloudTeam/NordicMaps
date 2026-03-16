<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\PaymentMethods;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Session
{
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteRepositoryInterface $klarnaQuoteRepository,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger
    ) {
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * Returns payment methods based on the information from the session
     *
     * @return array
     */
    public function getPaymentMethods(): array
    {
        $klarnaQuote = $this->loadKlarnaQuote();
        return $klarnaQuote ? $klarnaQuote->getPaymentMethods() : [];
    }

    /**
     * Getting back the Magento quote ID which is linked to the Klarna session
     *
     * @return null|string
     */
    public function getMagentoQuoteId(): ?string
    {
        $klarnaQuote = $this->loadKlarnaQuote();
        if ($klarnaQuote === null) {
            return null;
        }
        return $klarnaQuote->getQuoteId();
    }

    /**
     * Returns payment methods information based on the information from the session
     *
     * @return array
     */
    public function getPaymentMethodInformation(): array
    {
        $klarnaQuote = $this->loadKlarnaQuote();
        return $klarnaQuote ? $klarnaQuote->getPaymentMethodInfo() : [];
    }

    /**
     * Loading the Klarna quote
     *
     * @return null|QuoteInterface
     */
    private function loadKlarnaQuote(): ?QuoteInterface
    {
        $quoteId = $this->checkoutSession->getQuoteId();
        if ($quoteId === null) {
            $this->logger->forceLogging(
                'No quote is linked to the session but something ' .
                'tried to fetch a Klarna quote from the database. Returning empty result.'
            );
            return null;
        }

        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuoteId($quoteId);
        } catch (NoSuchEntityException $e) {
            $this->logger->forceLogging(
                'No active Klarna quote was found for the Magento quote ID: ' . $quoteId
            );
            $this->logger->logException($e);
            return null;
        }

        return $klarnaQuote;
    }
}
