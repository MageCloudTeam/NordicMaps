<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Model\Update;

use Klarna\Base\Model\Quote\PaymentMethod;
use Klarna\Base\Model\Quote\ShippingMethod\SelectionAssurance;
use Klarna\Kec\Model\Initialization\QuoteFetcher;
use Klarna\Kec\Model\Session;
use Klarna\Kec\Model\Update\Address\Coordinator;
use Klarna\Kec\Model\Update\KlarnaQuote as KlarnaQuoteUpdate;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Handler
{
    /**
     * @var PaymentMethod
     */
    private PaymentMethod $paymentMethod;
    /**
     * @var KlarnaQuote
     */
    private KlarnaQuoteUpdate $klarnaQuoteUpdate;
    /**
     * @var SelectionAssurance
     */
    private SelectionAssurance $selectionAssurance;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;
    /**
     * @var Coordinator
     */
    private Coordinator $coordinator;
    /**
     * @var QuoteFetcher
     */
    private QuoteFetcher $quoteFetcher;
    /**
     * @var Session
     */
    private Session $kecSession;
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @param PaymentMethod $paymentMethod
     * @param KlarnaQuote $klarnaQuoteUpdate
     * @param SelectionAssurance $selectionAssurance
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @param Coordinator $coordinator
     * @param QuoteFetcher $quoteFetcher
     * @param Session $kecSession
     * @param CheckoutSession $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(
        PaymentMethod $paymentMethod,
        KlarnaQuoteUpdate $klarnaQuoteUpdate,
        SelectionAssurance $selectionAssurance,
        CartRepositoryInterface $magentoQuoteRepository,
        Coordinator $coordinator,
        QuoteFetcher $quoteFetcher,
        Session $kecSession,
        CheckoutSession $checkoutSession
    ) {
        $this->paymentMethod = $paymentMethod;
        $this->klarnaQuoteUpdate = $klarnaQuoteUpdate;
        $this->selectionAssurance = $selectionAssurance;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
        $this->coordinator = $coordinator;
        $this->quoteFetcher = $quoteFetcher;
        $this->kecSession = $kecSession;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Updating the Magento quote by given Klarna address data
     *
     * @param array $parameter
     * @param array $klarnaData
     * @param string $clientToken
     * @return CartInterface
     */
    public function updateMagentoQuoteByKlarnaAddressData(
        array $parameter,
        array $klarnaData,
        string $clientToken
    ): CartInterface {
        $magentoQuote = $this->quoteFetcher->getMagentoQuote($parameter);

        $this->coordinator->updateAddress($klarnaData, $magentoQuote);
        $this->selectionAssurance->ensureShippingMethodSelectedWithPreCollect($magentoQuote);
        $this->paymentMethod->setPaymentMethod(
            $magentoQuote,
            Kp::ONE_KLARNA_PAYMENT_METHOD_CODE_WITH_PREFIX
        );

        $this->magentoQuoteRepository->save($magentoQuote);

        $this->klarnaQuoteUpdate->disableByMagentoQuote($magentoQuote);
        $this->klarnaQuoteUpdate->create((string) $magentoQuote->getId(), $clientToken);

        $this->kecSession->saveQuoteIds($magentoQuote->getId(), $this->checkoutSession->getQuoteId());
        $this->checkoutSession->setQuoteId($this->kecSession->getNewQuoteId());
        return $magentoQuote;
    }
}
