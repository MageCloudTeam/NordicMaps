<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model;

use Klarna\Base\Api\OrderInterface;
use Klarna\Base\Api\OrderRepositoryInterface;
use Klarna\Base\Model\ResourceModel\Order as OrderResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;

/**
 * @internal
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var OrderResource
     */
    private $resourceModel;

    /**
     * OrderRepository constructor.
     *
     * @param OrderFactory  $orderFactory
     * @param OrderResource $resourceModel
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        OrderFactory $orderFactory,
        OrderResource $resourceModel
    ) {
        $this->orderFactory = $orderFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritDoc
     */
    public function save(OrderInterface $order)
    {
        try {
            $this->resourceModel->save($order);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $order;
    }

    /**
     * @inheritDoc
     */
    public function getByKlarnaOrderId($klarnaOrderId)
    {
        $order = $this->orderFactory->create();

        $orderId = $this->resourceModel->getIdByKlarnaOrderId($klarnaOrderId);
        if (!$orderId) {
            throw new NoSuchEntityException(__('No Order with klarna_order_id  "%1" was found.', $orderId));
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }

    /**
     * Getting an empty order instance
     *
     * @return OrderInterface
     */
    public function createEmptyOrder(): OrderInterface
    {
        return $this->orderFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function getByOrder(MageOrder $mageOrder)
    {
        $order = $this->createEmptyOrder();

        $orderId = $this->resourceModel->getIdByOrder($mageOrder);
        if (!$orderId) {
            throw new NoSuchEntityException(__('Requested order doesn\'t exist.'));
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        $order = $this->orderFactory->create();
        $this->resourceModel->load($order, $id);
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with ID "%1" does not exist.', $id));
        }
        return $order;
    }

    /**
     * @inheritDoc
     */
    public function getByReservationId($reservationId)
    {
        $order = $this->orderFactory->create();

        $orderId = $this->resourceModel->getIdByReservationId($reservationId);
        if (!$orderId) {
            throw new NoSuchEntityException(__('Order with Reservation ID "%1" does not exist.', $reservationId));
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }

    /**
     * @inheritDoc
     */
    public function getBySessionId($sessionId)
    {
        $order = $this->orderFactory->create();

        $orderId = $this->resourceModel->getIdBySessionId($sessionId);
        if (!$orderId) {
            throw new NoSuchEntityException(__('Order with session_id "%1" does not exist.', $sessionId));
        }
        $this->resourceModel->load($order, $orderId);
        return $order;
    }
}
