<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Controller\Klarna;

use Klarna\Base\Controller\CsrfAbstract;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Logger\Model\Logger;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Klarna\Base\Model\Responder\Result;
use \Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ObjectManager;

/**
 * @api
 */
class QuoteStatus extends CsrfAbstract implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @param Result $result
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        CartRepositoryInterface $magentoQuoteRepository,
        Result $result,
        LoggerInterface $logger = null
    ) {
        $this->request = $request;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
        $this->result = $result;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(
            Logger::class
        );
    }

    /**
     * Getting back information from the quote, if the quote was active then we will be allowed to place the order from
     * the website frontend and if it was not active it means the quote's respective order has already been placed,
     * and we don't need to place an order from the frontend
     *
     * @return Json
     */
    public function execute()
    {
        $result = ['is_active' => '1'];
        $rawParameter = json_decode($this->request->getContent(), true);

        if (!isset($rawParameter['authorization_token'])) {
            $this->logger->forceLogging('No authorization token given');
            return $this->result->getJsonResult(400, $result);
        }

        $authorizationToken = $rawParameter['authorization_token'];
        if (empty($authorizationToken)) {
            $this->logger->forceLogging('authorization token is empty');
            return $this->result->getJsonResult(400, $result);
        }

        try {
            $maxTime = 25;
            $sleepDuration = 0;
            do {
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                sleep(2);
                $sleepDuration += 2;
                $klarnaQuote = $this->klarnaQuoteRepository->getByAuthorizationToken($authorizationToken);
            } while ($klarnaQuote->isAuthCallbackInProgress() && $sleepDuration < $maxTime);
        } catch (NoSuchEntityException $e) {
            $this->logger->forceLogging(
                'Could not check the authorization callback workflow status. Reason: ' . $e->getMessage()
            );
            $this->logger->logException($e);
            return $this->result->getJsonResult(200, $result);
        }

        $magentoQuote = $this->magentoQuoteRepository->get($klarnaQuote->getQuoteId());
        $result['is_active'] = $magentoQuote->getIsActive() && $klarnaQuote->isAuthCallbackFailedOrNotStarted();

        return $this->result->getJsonResult(200, $result);
    }
}
