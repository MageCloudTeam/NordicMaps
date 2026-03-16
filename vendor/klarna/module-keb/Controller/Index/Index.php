<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Keb\Controller\Index;

use Klarna\Keb\Model\CallbackHandlerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Session;
use Klarna\Base\Exception;
use Klarna\Base\Model\Api\Exception as ApiException;
use Psr\Log\LoggerInterface;

/**
 * @api
 */
class Index implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;
    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var CallbackHandlerInterface
     */
    private CallbackHandlerInterface $callbackHandler;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param RequestInterface         $request
     * @param ResultFactory            $resultFactory
     * @param UrlInterface             $url
     * @param Session                  $session
     * @param CallbackHandlerInterface $callbackHandler
     * @param LoggerInterface          $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface         $request,
        ResultFactory            $resultFactory,
        UrlInterface             $url,
        Session                  $session,
        CallbackHandlerInterface $callbackHandler,
        LoggerInterface          $logger
    ) {
        $this->request         = $request;
        $this->resultFactory   = $resultFactory;
        $this->url             = $url;
        $this->session         = $session;
        $this->callbackHandler = $callbackHandler;
        $this->logger          = $logger;
    }

    /**
     * We handle here the ajax request, that is providing us the callback address data.
     *
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     * @throws ApiException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $quote    = $this->session->getQuote();
        $postData = $this->request->getPostValue();

        try {
            $method = $this->callbackHandler->handle($quote, $postData);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON)
            ->setHttpResponseCode(200);

        $result->setData(
            [
                'success' => true,
                'url'     => $this->url->getUrl('checkout', ['_fragment' => 'payment']),
                'method'  => $method ?? '',
            ]
        );

        return $result;
    }
}
