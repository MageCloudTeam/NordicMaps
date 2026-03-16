<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Observer;

use MageCloud\AutoInvoice\Api\InvoiceProcessInterface;
use MageCloud\AutoInvoice\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Shipment;
use Psr\Log\LoggerInterface;

/**
 * Class CreateInvoiceProcessor
 */
class CreateInvoiceProcessor implements ObserverInterface
{
    /**
     * @var InvoiceProcessInterface
     */
    private $invoiceProcess;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CreateInvoiceProcessor constructor.
     *
     * @param Config $config
     * @param InvoiceProcessInterface $invoiceProcess
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        InvoiceProcessInterface $invoiceProcess,
        LoggerInterface $logger
    ) {
        $this->invoiceProcess = $invoiceProcess;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();

        if ($this->config->isEnabled() === false) {
            $this->logger->info('Auto Invoice Disabled');

            return;
        }

        if ($shipment->getOrigData('entity_id')) {
            $this->logger->info('Shipment is not new');

            return;
        }

        if ($item = $this->invoiceProcess->getItemToProcess($shipment->getOrder())) {
            $this->invoiceProcess->invoice($item);
        } else {
            $this->logger->info(__('Invoice Process Item Not Found. (Please check configuration). Oder ID: %1', $shipment->getOrderId()));
        }
    }
}