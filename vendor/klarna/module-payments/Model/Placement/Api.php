<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Placement;

use Klarna\Base\Exception as KlarnaBaseException;
use Klarna\Base\Model\Api\Exception as KlarnaApiException;
use Klarna\Kp\Api\CreditApiInterface;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Model\Api\Builder\Request;
use Magento\Quote\Api\Data\CartInterface;
use Klarna\Base\Api\BuilderInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Api
{
    /**
     * @var CreditApiInterface
     */
    private CreditApiInterface $api;
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @param CreditApiInterface $api
     * @param Request $request
     * @codeCoverageIgnore
     */
    public function __construct(CreditApiInterface $api, Request $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    /**
     * Placing the order through the API
     *
     * @param QuoteInterface $klarnaQuote
     * @param CartInterface $magentoQuote
     * @param OrderInterface $order
     * @return ResponseInterface
     * @throws KlarnaApiException
     * @throws KlarnaBaseException
     */
    public function placeKlarnaOrder(
        QuoteInterface $klarnaQuote,
        CartInterface $magentoQuote,
        OrderInterface $order
    ): ResponseInterface {
        $placeOrderRequest = $this->request->generatePlaceOrderRequest(
            $magentoQuote,
            $klarnaQuote->getAuthTokenCallbackToken()
        );

        $this->api->setKlarnaQuote($klarnaQuote);
        $placeKlarnaOrderResponse = $this->api->placeOrder(
            $klarnaQuote->getAuthorizationToken(),
            $placeOrderRequest,
            $klarnaQuote->getSessionId(),
            $order->getIncrementId()
        );

        if ($placeKlarnaOrderResponse->isSuccessfull()) {
            if ($placeKlarnaOrderResponse->getRedirectUrl()) {
                $klarnaQuote->setRedirectUrl($placeKlarnaOrderResponse->getRedirectUrl());
            }
            return $placeKlarnaOrderResponse;
        }

        $this->api->setKlarnaQuote($klarnaQuote);
        $cancelKlarnaOrderResponse = $this->api->cancelOrder(
            $klarnaQuote->getAuthorizationToken(),
            $klarnaQuote->getSessionId()
        );
        if ($cancelKlarnaOrderResponse->isSuccessfull()) {
            throw new KlarnaBaseException(__('Unable to authorize payment for this order.'));
        }

        $message = $cancelKlarnaOrderResponse->getMessage()
            ?: __('Unable to release authorization for the token %1', $klarnaQuote->getAuthorizationToken());
        throw new KlarnaApiException($message);
    }
}
