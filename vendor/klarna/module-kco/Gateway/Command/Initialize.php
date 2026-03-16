<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Gateway\Command;

use Klarna\Base\Helper\ConfigHelper;
use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Kco\Model\Checkout\Kco\Initializer as kcoInitializer;
use Klarna\Kco\Model\Configuration\Checkout;
use Klarna\Kco\Model\Payment\Kco as KcoMethod;
use Magento\Framework\DataObject;
use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Model\Order;

/**
 * @internal
 */
class Initialize implements CommandInterface
{
    public const TYPE_AUTH = 'authorization';

    /**
     * @var kcoInitializer
     */
    private $kcoInitializer;

    /**
     * @var ConfigHelper
     */
    protected $helper;
    /**
     * @var KlarnaConfig
     */
    private $klarnaConfig;
    /**
     * @var Checkout
     */
    private Checkout $checkoutConfiguration;

    /**
     * Initialize constructor.
     *
     * @param ConfigHelper    $helper
     * @param KlarnaConfig    $klarnaConfig
     * @param kcoInitializer  $kcoInitializer
     * @param Checkout        $checkoutConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        ConfigHelper $helper,
        KlarnaConfig $klarnaConfig,
        kcoInitializer $kcoInitializer,
        Checkout $checkoutConfiguration
    ) {
        $this->kcoInitializer = $kcoInitializer;
        $this->helper = $helper;
        $this->klarnaConfig = $klarnaConfig;
        $this->checkoutConfiguration = $checkoutConfiguration;
    }

    /**
     * Initialize command
     *
     * @param array $commandSubject
     *
     * @return null|Command\ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(array $commandSubject)
    {
        /** @var \Magento\Payment\Model\InfoInterface $payment */
        $payment = $commandSubject['payment']->getPayment();
        /** @var DataObject $stateObject */
        $stateObject = $commandSubject['stateObject'];
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        /** @var \Magento\Store\Model\Store $store */
        $store = $order->getStore();
        $state = Order::STATE_PROCESSING;
        $status = $this->checkoutConfiguration->getOrderStatusForNewOrders($store);
        if (0 >= $order->getGrandTotal()) {
            $state = Order::STATE_NEW;
        } elseif ($this->klarnaConfig->getVersionConfig($store)->isPaymentReview()) {
            $status = 'pending_payment';
            $state = Order::STATE_PAYMENT_REVIEW;
        }
        $stateObject->setState($state);
        $stateObject->setStatus($status);

        $stateObject->setIsNotified(false);

        $transactionId = $this->kcoInitializer->getReservationId($store);
        $payment->setBaseAmountAuthorized($order->getBaseTotalDue());
        $payment->setAmountAuthorized($order->getTotalDue());
        $payment->setTransactionId($transactionId)->setIsTransactionClosed(0);
        $payment->addTransaction(self::TYPE_AUTH);

        return null;
    }
}
