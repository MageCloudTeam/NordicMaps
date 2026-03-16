<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Controller\Klarna;

use Klarna\Kp\Api\AuthorizationCallbackStatusInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Placement\AuthorizationCallback\RequestValidator;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Klarna\Base\Model\Responder\Result;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Json;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Kp\Model\Logger\Authorize as AuthorizeLogger;
use Klarna\Base\Controller\CsrfAbstract;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\App\ObjectManager;

/**
 * @api
 */
class Authorize extends CsrfAbstract implements HttpPostActionInterface
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var AuthorizeLogger
     */
    private AuthorizeLogger $authorizeLogger;
    /**
     * @var CartManagementInterface
     */
    private CartManagementInterface $cartManagement;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $magentoQuoteRepository;

    /**
     * @var RequestValidator|null
     */
    private ?RequestValidator $requestValidator;

    /**
     * @param RequestInterface $request
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param Result $result
     * @param LoggerInterface $logger
     * @param AuthorizeLogger $authorizeLogger
     * @param CartManagementInterface $cartManagement
     * @param CartRepositoryInterface $magentoQuoteRepository
     * @param RequestValidator $requestValidator
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        Result $result,
        LoggerInterface $logger,
        AuthorizeLogger $authorizeLogger,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $magentoQuoteRepository,
        RequestValidator $requestValidator = null
    ) {
        $this->request = $request;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->result = $result;
        $this->logger = $logger;
        $this->authorizeLogger = $authorizeLogger;
        $this->cartManagement = $cartManagement;
        $this->magentoQuoteRepository = $magentoQuoteRepository;
        $this->requestValidator = $requestValidator ?: ObjectManager::getInstance()->get(
            RequestValidator::class
        );
    }

    /**
     * Performing the authorization callback action
     *
     * @return Json
     */
    public function execute()
    {
        try {
            $parameters = json_decode($this->request->getContent(), true);

            if ($this->request->getParam('dryRun')) {
                return $this->result->getJsonResult(
                    200,
                    [
                        'message' => 'The ' . $this->request->getRequestUri() . ' is accessible.',
                        'timestamp' => time(),
                        'code' => 200
                    ]
                );
            }

            $this->requestValidator->validateRequestBody();
            $this->logTheCall($parameters['session_id']);

            $klarnaQuote = $this->klarnaQuoteRepository->getBySessionId($parameters['session_id']);

            $this->requestValidator->verifyAuthCallbackToken($klarnaQuote);
            $this->requestValidator->verifyMagentoQuote((string) $klarnaQuote->getQuoteId());

            if ($klarnaQuote->isAuthCallbackAlreadyProcessed()) {
                return $this->result->getJsonResult(400, [
                    'error' => 'Another authorization callback workflow is still in progress.'
                ]);
            }

            $klarnaQuote->setAuthorizationToken($parameters['authorization_token']);
            $klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::IN_PROGRESS);
            $this->klarnaQuoteRepository->save($klarnaQuote);

            $this->cartManagement->placeOrder($klarnaQuote->getQuoteId());
        } catch (LocalizedException $exception) {
            if (isset($klarnaQuote)) {
                $klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::FAILED);
                $this->klarnaQuoteRepository->save($klarnaQuote);
            }

            $this->authorizeLogger->logException($parameters['session_id'] ?? '', $this->request, $exception);

            return $this->result->getJsonResult(400, ['error' => $exception->getMessage()]);
        }

        $klarnaQuote->setAuthCallbackActiveCurrentStatus(AuthorizationCallbackStatusInterface::SUCCESSFUL);
        $this->klarnaQuoteRepository->save($klarnaQuote);

        return $this->result->getJsonResult(204, ['message' => 'Order has placed successfully.']);
    }

    /**
     * Log the api call details
     *
     * @param string $sessionId
     * @return void
     */
    private function logTheCall(string $sessionId)
    {
        $this->logger->setRequestContext($this->request);
        $this->logger->debug(sprintf('Authorization callback for session ID: %s.', $sessionId));
        $this->authorizeLogger->logRequest($sessionId, $this->request);
    }
}
