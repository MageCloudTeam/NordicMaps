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
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Model\Api\Builder\Request;
use Klarna\Kp\Model\QuoteFactory as KlarnaQuoteFactory;
use Magento\Framework\Math\Random;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\PaymentMethods\PaymentMethodProvider;

/**
 * @internal
 */
class KlarnaQuote
{
    /**
     * @var KlarnaQuoteFactory
     */
    private KlarnaQuoteFactory $klarnaQuoteFactory;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var PaymentMethodProvider
     */
    private PaymentMethodProvider $paymentMethodProvider;
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var CreditApiInterface
     */
    private CreditApiInterface $api;
    /**
     * @var Random
     */
    private Random $randomGenerator;

    /**
     * @param KlarnaQuoteFactory $klarnaQuoteFactory
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param PaymentMethodProvider $paymentMethodProvider
     * @param Request $request
     * @param CreditApiInterface $api
     * @param Random $randomGenerator
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaQuoteFactory $klarnaQuoteFactory,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        PaymentMethodProvider $paymentMethodProvider,
        Request $request,
        CreditApiInterface $api,
        Random $randomGenerator,
    ) {
        $this->klarnaQuoteFactory = $klarnaQuoteFactory;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->request = $request;
        $this->api = $api;
        $this->randomGenerator = $randomGenerator;
    }

    /**
     * Marking the Klarna quote as inactive
     *
     * @param string $klarnaSessionId
     */
    public function markInactiveByKlarnaSessionId(string $klarnaSessionId): void
    {
        $klarnaQuote = $this->klarnaQuoteRepository->getBySessionId($klarnaSessionId);
        $this->klarnaQuoteRepository->markInactive($klarnaQuote);
    }

    /**
     * Creating a new Klarna quote
     *
     * @param CartInterface $quote
     * @return QuoteInterface
     */
    public function createNewKlarnaQuote(CartInterface $quote): QuoteInterface
    {
        $authCallbackToken = $this->randomGenerator->getUniqueHash();
        $createSessionRequest = $this->request->generateCreateSessionRequest($quote, $authCallbackToken);
        $klarnaResponse = $this->api->createSession($createSessionRequest);

        if (!$klarnaResponse->isSuccessfull()) {
            throw new KlarnaApiException(__('Unable to initialize Klarna payments session'));
        }

        $klarnaQuote = $this->klarnaQuoteFactory->create();
        $klarnaQuote->setSessionId($klarnaResponse->getSessionId());
        $klarnaQuote->setClientToken($klarnaResponse->getClientToken());
        $klarnaQuote->setIsActive(1);
        $klarnaQuote->setQuoteId($quote->getId());
        $klarnaQuote->setPaymentMethods($this->paymentMethodProvider->extractByApiResponse($klarnaResponse));
        $klarnaQuote->setPaymentMethodInfo($klarnaResponse->getPaymentMethodCategories());
        $klarnaQuote->setAuthTokenCallbackToken($authCallbackToken);

        $klarnaRequestData = $createSessionRequest->toArray();
        if (isset($klarnaRequestData['customer'])) {
            $klarnaQuote->setCustomerType($klarnaRequestData['customer']['type']);
        }

        $this->klarnaQuoteRepository->save($klarnaQuote);
        return $klarnaQuote;
    }

    /**
     * Updating the Klarna quote
     *
     * @param CartInterface $magentoQuote
     * @param QuoteInterface $klarnaQuote
     * @return QuoteInterface
     */
    public function updateKlarnaQuote(CartInterface $magentoQuote, QuoteInterface $klarnaQuote): QuoteInterface
    {
        $sessionId = $klarnaQuote->getSessionId();
        $updateSessionRequest = $this
            ->request
            ->generateUpdateSessionRequest($magentoQuote, $klarnaQuote->getAuthTokenCallbackToken());

        $this->api->setKlarnaQuote($klarnaQuote);
        $klarnaUpdateResponse = $this->api->updateSession($sessionId, $updateSessionRequest);

        if (!$klarnaUpdateResponse->isSuccessfull()) {
            if ($klarnaUpdateResponse->isExpired()) {
                $this->markInactiveByKlarnaSessionId($sessionId);
                return $klarnaQuote;
            }
            throw new KlarnaApiException(__('Unable to initialize Klarna payments session'));
        }

        $klarnaQuote = $this->klarnaQuoteRepository->getBySessionId($sessionId);
        $klarnaQuote->setPaymentMethods($this->paymentMethodProvider->extractByApiResponse($klarnaUpdateResponse));
        $klarnaQuote->setPaymentMethodInfo($klarnaUpdateResponse->getPaymentMethodCategories());

        $this->klarnaQuoteRepository->save($klarnaQuote);
        return $klarnaQuote;
    }
}
