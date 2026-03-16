<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Placement;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class Authorize
{
    /**
     * @var Api
     */
    private Api $api;
    /**
     * @var FraudChecker
     */
    private FraudChecker $fraudChecker;
    /**
     * @var ShopUpdate
     */
    private ShopUpdate $shopUpdate;
    /**
     * @var OrderUpdate
     */
    private OrderUpdate $orderUpdate;
    /**
     * @var QuoteRepositoryInterface
     */
    private QuoteRepositoryInterface $klarnaQuoteRepository;

    /**
     * @param Api $api
     * @param FraudChecker $fraudChecker
     * @param ShopUpdate $shopUpdate
     * @param OrderUpdate $orderUpdate
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        Api $api,
        FraudChecker $fraudChecker,
        ShopUpdate $shopUpdate,
        OrderUpdate $orderUpdate,
        QuoteRepositoryInterface $klarnaQuoteRepository
    ) {
        $this->api = $api;
        $this->fraudChecker = $fraudChecker;
        $this->shopUpdate = $shopUpdate;
        $this->orderUpdate = $orderUpdate;
        $this->klarnaQuoteRepository = $klarnaQuoteRepository;
    }

    /**
     * Performing the authorization
     *
     * @param QuoteInterface $klarnaQuote
     * @param CartInterface $magentoQuote
     * @param OrderInterface $order
     * @param InfoInterface $payment
     * @throws \Klarna\Base\Exception
     * @throws \Klarna\Base\Model\Api\Exception
     */
    public function performAuthorization(
        QuoteInterface $klarnaQuote,
        CartInterface $magentoQuote,
        OrderInterface $order,
        InfoInterface $payment
    ): void {
        if ($klarnaQuote->getOrderId() !== null) {
            return;
        }
        $placeKlarnaOrderResponse = $this->api->placeKlarnaOrder($klarnaQuote, $magentoQuote, $order);
        $this->fraudChecker->checkFraud($placeKlarnaOrderResponse, $payment);
        $this->shopUpdate->updateOrderDatabases($payment, $order, $placeKlarnaOrderResponse);

        $this->orderUpdate->updateAddresses($placeKlarnaOrderResponse->getOrderId(), $order);

        $klarnaQuote->setOrderId($order->getId());
        $this->klarnaQuoteRepository->save($klarnaQuote);
    }
}
