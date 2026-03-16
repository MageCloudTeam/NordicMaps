<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Model;

use Exception;
use Hryvinskyi\Base\Helper\VarDumper;
use MageCloud\AutoInvoice\Api\Data\InvoiceProcessItemInterface;
use MageCloud\AutoInvoice\Api\Data\InvoiceProcessItemInterfaceFactory;
use MageCloud\AutoInvoice\Api\InvoiceProcessInterface;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Helper\Data as SalesData;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusCollectionFactory;
use Magento\Sales\Model\Service\InvoiceServiceFactory;
use Psr\Log\LoggerInterface;

/**
 * Class InvoiceProcess
 */
class InvoiceProcess implements InvoiceProcessInterface
{
    /**
     * @var InvoiceServiceFactory
     */
    private $invoiceServiceFactory;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var OrderStatusCollectionFactory
     */
    private $orderStatusCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var InvoiceProcessItemInterfaceFactory
     */
    private $invoiceProcessItemFactory;

    /**
     * @var SalesData
     */
    private $salesData;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array|null
     */
    private $orderStatusToStateMap;

    /**
     * InvoiceProcess constructor.
     *
     * @param InvoiceServiceFactory $invoiceServiceFactory
     * @param Transaction $transaction
     * @param OrderStatusCollectionFactory $orderStatusCollectionFactory
     * @param Config $config
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param InvoiceProcessItemInterfaceFactory $invoiceProcessItemFactory
     * @param SalesData $salesData
     * @param InvoiceSender $invoiceSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        InvoiceServiceFactory $invoiceServiceFactory,
        Transaction $transaction,
        OrderStatusCollectionFactory $orderStatusCollectionFactory,
        Config $config,
        OrderCollectionFactory $orderCollectionFactory,
        InvoiceProcessItemInterfaceFactory $invoiceProcessItemFactory,
        SalesData $salesData,
        InvoiceSender $invoiceSender,
        LoggerInterface $logger
    ) {
        $this->invoiceServiceFactory = $invoiceServiceFactory;
        $this->transaction = $transaction;
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->config = $config;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->invoiceProcessItemFactory = $invoiceProcessItemFactory;
        $this->salesData = $salesData;
        $this->invoiceSender = $invoiceSender;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getItemToProcess(Order $order): ?InvoiceProcessItemInterface
    {
        $rules = $this->config->getParsedProcessingRules($order->getStoreId());

        if ($order->getTotalInvoiced() !== null) {
            return null;
        }

        foreach ($rules as $rule) {
            if (
                $rule[Config::RULE_PAYMENT_METHOD] != Config::RULE_PAYMENT_METHOD_ALL &&
                $rule[Config::RULE_PAYMENT_METHOD] != $this->getPaymentMethodCode($order)
            ) {
                continue;
            }

            return $this->invoiceProcessItemFactory->create()
                ->setOrder($order)
                ->setDestinationStatus($rule[Config::RULE_DESTINATION_STATUS])
                ->setCaptureMode($rule[Config::RULE_CAPTURE_MODE])
                ->setEmailCopyOfInvoice(!!$rule[Config::RULE_EMAIL_COPY_OF_INVOICE]);
        }

        return null;
    }

    /**
     * Returns payment method code of the given order
     *
     * @param Order $order
     *
     * @return string
     */
    private function getPaymentMethodCode(Order $order): string
    {
        try {
            return $order->getPayment()->getMethodInstance()->getCode();
        } catch (Exception $exception) {
            $this->logger->info('Payment code not returned.', $exception->getTrace());
            return '';
        }
    }

    /**
     * @inheritdoc
     */
    public function invoice(InvoiceProcessItemInterface $item): void
    {
        $order = $item->getOrder();

        $status = $item->getDestinationStatus();
        $order->setStatus($status);

        $state = $this->getOrderStateByStatus($status);
        if ($state) {
            $order->setState($state);
        }

        $invoice = $this->invoiceServiceFactory->create()
            ->prepareInvoice($order);
        $invoice->setRequestedCaptureCase($item->getCaptureMode());
        $invoice->register();

        $invoice->getOrder()->setCustomerNoteNotify($item->getEmailCopyOfInvoice());

        $transactionSave = $this->transaction
            ->addObject($invoice)
            ->addObject($order);

        $transactionSave->save();
        $order->addCommentToStatusHistory(
            __(
                "Invoice #%1 created automatically. Data: [Is Send Email: %2; Capture Mode: %3; Status changed to: %4]",
                $invoice->getIncrementId(),
                VarDumper::export($item->getEmailCopyOfInvoice()),
                $item->getCaptureMode(),
                $item->getDestinationStatus()
            )
        );

        // send invoice/shipment emails
        try {
            if ($item->getEmailCopyOfInvoice() && $this->salesData->canSendNewInvoiceEmail()) {
                $this->invoiceSender->send($invoice);
                $order->addCommentToStatusHistory(__('Invoice #%1 email send success', $invoice->getIncrementId()));
            } else {
                $this->logger->info(
                    __(
                        'Invoice send email disabled. Configuration: %1, Can Send: %2',
                        VarDumper::export($item->getEmailCopyOfInvoice()),
                        VarDumper::export($this->salesData->canSendNewInvoiceEmail())
                    )
                );
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * Return the order state given a status
     *
     * @param string $status
     *
     * @return bool|string
     */
    private function getOrderStateByStatus(string $status): ?string
    {
        $map = $this->getOrderStatusToStateMap();

        return empty($map[$status]) ? null : $map[$status];
    }

    /**
     * Returns the order status to state map
     */
    private function getOrderStatusToStateMap(): array
    {
        if (!is_null($this->orderStatusToStateMap)) {
            return $this->orderStatusToStateMap;
        }

        $collection = $this->orderStatusCollectionFactory->create()
            ->joinStates();

        $this->orderStatusToStateMap = [];
        foreach ($collection as $status) {
            $this->orderStatusToStateMap[$status->getStatus()] = $status->getState();
        }

        return $this->orderStatusToStateMap;
    }
}