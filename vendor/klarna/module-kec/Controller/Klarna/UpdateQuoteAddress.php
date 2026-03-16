<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Controller\Klarna;

use Klarna\Base\Controller\CsrfAbstract;
use Klarna\Kec\Model\Update\Handler;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Klarna\Base\Model\Responder\Result;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\UrlInterface;

/**
 * @api
 */
class UpdateQuoteAddress extends CsrfAbstract implements HttpPostActionInterface
{
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var Handler
     */
    private Handler $handler;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @param Result $result
     * @param RequestInterface $request
     * @param Handler $handler
     * @param LoggerInterface $logger
     * @param UrlInterface $urlBuilder
     * @codeCoverageIgnore
     */
    public function __construct(
        Result $result,
        RequestInterface $request,
        Handler $handler,
        LoggerInterface $logger,
        UrlInterface $urlBuilder
    ) {
        $this->result = $result;
        $this->request = $request;
        $this->handler = $handler;
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $rawParameter = $this->request->getParams();
            $rawParameter['additional_input'] = json_decode($rawParameter['additional_input'], true);
            $rawParameter['addresses'] = json_decode($rawParameter['addresses'], true);

            $this->handler->updateMagentoQuoteByKlarnaAddressData(
                $rawParameter,
                $rawParameter['addresses'],
                $rawParameter['client_token'],
            );

            $httpCode = 200;
            $result = [];
            $result['url'] = $this->urlBuilder->getUrl('checkout', ['_fragment' => 'payment']);
            $result['method'] = Kp::ONE_KLARNA_PAYMENT_METHOD_CODE_WITH_PREFIX;
        } catch (\Exception $e) {
            $this->logger->logException($e);
            $result['error_message'] = $e->getMessage();
            $httpCode = 400;
        }

        $result['status'] = $httpCode;
        return $this->result->getJsonResult($httpCode, $result);
    }
}
