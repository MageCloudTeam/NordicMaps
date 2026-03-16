<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Placement;

use Klarna\Base\Api\OrderRepositoryInterface;
use Klarna\Base\Exception as KlarnaBaseException;
use Klarna\Kp\Api\Data\ResponseInterface;
use Klarna\Kp\Model\Configuration\Payment;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Klarna\Base\Model\OrderFactory;
use Klarna\Base\Model\Configuration\Api;

/**
 * @internal
 */
class ShopUpdate
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $klarnaOrderRepository;
    /**
     * @var MagentoOrderRepositoryInterface
     */
    private MagentoOrderRepositoryInterface $magentoOrderRepository;
    /**
     * @var OrderFactory
     */
    private OrderFactory $klarnaOrderFactory;
    /**
     * @var Payment
     */
    private Payment $paymentConfiguration;
    /**
     * @var Api
     */
    private Api $apiConfiguration;

    /**
     * @param OrderRepositoryInterface $klarnaOrderRepository
     * @param MagentoOrderRepositoryInterface $magentoOrderRepository
     * @param OrderFactory $klarnaOrderFactory
     * @param Payment $paymentConfiguration
     * @param Api $apiConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        OrderRepositoryInterface $klarnaOrderRepository,
        MagentoOrderRepositoryInterface $magentoOrderRepository,
        OrderFactory $klarnaOrderFactory,
        Payment $paymentConfiguration,
        Api $apiConfiguration
    ) {
        $this->klarnaOrderRepository = $klarnaOrderRepository;
        $this->magentoOrderRepository = $magentoOrderRepository;
        $this->klarnaOrderFactory = $klarnaOrderFactory;
        $this->paymentConfiguration = $paymentConfiguration;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * Updating the Klarna and Magento order table
     *
     * @param InfoInterface $payment
     * @param OrderInterface $order
     * @param ResponseInterface $response
     * @throws KlarnaBaseException
     */
    public function updateOrderDatabases(
        InfoInterface $payment,
        OrderInterface $order,
        ResponseInterface $response
    ): void {
        $payment->getMethodInstance()->setCode(Kp::METHOD_CODE);
        $order->getPayment()->setMethod(Kp::METHOD_CODE);
        $this->magentoOrderRepository->save($order);

        $klarnaOrderId = $response->getOrderId();
        $klarnaOrder = $this->klarnaOrderFactory->create();
        $klarnaOrder->setData([
            'klarna_order_id' => $klarnaOrderId,
            'reservation_id'  => $klarnaOrderId,
            'session_id'      => $response->getSessionId(),
            'order_id'        => $order->getId(),
            'used_mid'        => $this->apiConfiguration->getMerchantId($order->getStore()),
            'is_b2b'          => $this->paymentConfiguration->isB2bEnabled($order->getStore())
        ]);
        $this->klarnaOrderRepository->save($klarnaOrder);

        if ($klarnaOrder->getId() === null || $klarnaOrder->getReservationId() === null) {
            throw new KlarnaBaseException(__('Unable to authorize payment for this order.'));
        }

        $payment->setTransactionId($klarnaOrderId)->setIsTransactionClosed(0);
    }
}
