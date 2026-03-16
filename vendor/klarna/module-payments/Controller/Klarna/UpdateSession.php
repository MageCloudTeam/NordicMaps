<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Controller\Klarna;

use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Logger\Model\Logger;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Klarna\Base\Model\Responder\Result;
use \Magento\Framework\Controller\Result\Json;
use Klarna\Base\Controller\CsrfAbstract;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
class UpdateSession extends CsrfAbstract implements HttpPostActionInterface
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
     * @var Result
     */
    private Result $result;
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $magentoOrderRepository;
    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param Result $result
     * @param OrderRepositoryInterface $magentoOrderRepository
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        Result $result,
        OrderRepositoryInterface $magentoOrderRepository,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger = null
    ) {
        $this->request = $request;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->result = $result;
        $this->magentoOrderRepository = $magentoOrderRepository;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(
            Logger::class
        );
    }

    /**
     * Updating the checkout session
     *
     * @return Json
     */
    public function execute()
    {
        $rawParameter = json_decode($this->request->getContent(), true);
        if (!isset($rawParameter['authorization_token'])) {
            $this->logger->forceLogging('No authorization token given');
            return $this->result->getJsonResult(400);
        }

        $authorizationToken = $rawParameter['authorization_token'];
        if (empty($authorizationToken)) {
            $this->logger->forceLogging('authorization token is empty');
            return $this->result->getJsonResult(400);
        }

        try {
            $klarnaQuote = $this->klarnaQuoteRepository->getByAuthorizationToken($authorizationToken);
            $magentoOrder = $this->magentoOrderRepository->get($klarnaQuote->getOrderId());
        } catch (NoSuchEntityException $e) {
            $this->logger->forceLogging(
                'Could not update the session. Reason: ' . $e->getMessage()
            );
            $this->logger->logException($e);
            return $this->result->getJsonResult(400);
        }

        $this->checkoutSession->setLastQuoteId($klarnaQuote->getQuoteId());
        $this->checkoutSession->setLastSuccessQuoteId($klarnaQuote->getQuoteId());
        $this->checkoutSession->setLastOrderId($magentoOrder->getId());
        $this->checkoutSession->setLastRealOrderId($magentoOrder->getIncrementId());
        $this->checkoutSession->setLastOrderStatus($magentoOrder->getStatus());

        return $this->result->getJsonResult(204);
    }
}
