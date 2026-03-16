<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update;

use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Payment\Kp;
use Klarna\Kp\Model\Quote;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Klarna\Kp\Model\QuoteFactory;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class KlarnaQuote
{
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $klarnaQuoteFactory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param QuoteFactory $klarnaQuoteFactory
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        QuoteRepositoryInterface $klarnaQuoteRepository,
        QuoteFactory $klarnaQuoteFactory,
        LoggerInterface $logger
    ) {
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->klarnaQuoteFactory = $klarnaQuoteFactory;
        $this->logger = $logger;
    }

    /**
     * Disable entry by the given Magento quote
     *
     * @param CartInterface $quote
     */
    public function disableByMagentoQuote(CartInterface $quote): void
    {
        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($quote);
            $klarnaQuote->setIsActive(0);
            $this->klarnaQuoteRepository->save($klarnaQuote);
            $this->logger->info(
                'Disabled the active entry in database table klarna_payments_quote for the quote ID ' .
                $quote->getId() .
                ' and payments_quote_id ' .
                $klarnaQuote->getId()
            );
        } catch (NoSuchEntityException $e) {
            $this->logger->info(
                'No quote ID was found at the database table klarna_payments_quote for the quote ID ' .
                $quote->getId()
            );
        }
    }

    /**
     * Creating a Klarna quote entry
     *
     * @param string $magentoQuoteId
     * @param string $clientToken
     */
    public function create(string $magentoQuoteId, string $clientToken): void
    {
        /** @var Quote $klarnaQuote */
        $klarnaQuote = $this->klarnaQuoteFactory->create();
        $klarnaQuote->setClientToken($clientToken);
        $klarnaQuote->setQuoteId($magentoQuoteId);
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->setPaymentMethods(Kp::ONE_KLARNA_PAYMENT_METHOD_CODE);
        $klarnaQuote->setPaymentMethodInfo(Kp::ONE_KLARNA_PAYMENT_METHOD_INFO);
        $klarnaQuote->markAsKecSession();

        $this->klarnaQuoteRepository->save($klarnaQuote);
    }
}
