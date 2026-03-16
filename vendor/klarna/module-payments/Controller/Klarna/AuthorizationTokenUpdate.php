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
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\App\Action\HttpPutActionInterface;
use Magento\Checkout\Model\Session;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Base\Model\Responder\Result;
use Magento\Framework\App\RequestInterface;

/**
 * @api
 */
class AuthorizationTokenUpdate extends CsrfAbstract implements HttpPutActionInterface
{
    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Session $session
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param Result $result
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        Session $session,
        QuoteRepositoryInterface $klarnaQuoteRepository,
        Result $result,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
        $this->result = $result;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Updating the auth token of the Klarna quote
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $magentoQuote = $this->session->getQuote();

        $parameters = json_decode($this->request->getContent(), true);
        if (!isset($parameters['authorization_token'])) {
            $this->logger->forceLogging('Authorization token is missing for the Magento quote ID ' .
                $magentoQuote->getId());
            return $this->result->getJsonResult(400);
        }
        if (empty($parameters['authorization_token'])) {
            $this->logger->forceLogging('Authorization token is empty for the Magento quote ID ' .
                $magentoQuote->getId());
            return $this->result->getJsonResult(400);
        }

        $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($magentoQuote);
        $klarnaQuote->setAuthorizationToken($parameters['authorization_token']);
        $this->klarnaQuoteRepository->save($klarnaQuote);

        return $this->result->getJsonResult(200);
    }
}
