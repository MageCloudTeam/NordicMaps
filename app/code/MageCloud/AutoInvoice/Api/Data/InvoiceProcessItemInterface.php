<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Api\Data;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

interface InvoiceProcessItemInterface
{
    /**
     * Keys
     */
    const KEY_ORDER = 'order';
    const KEY_DESTINATION_STATUS = 'destination_status';
    const KEY_CAPTURE_MODE = 'capture_mode';
    const KEY_EMAIL_COPY_OF_INVOICE = 'email_copy_of_invoice';

    /**
     * Returns the order to invoice
     *
     * @returns Order
     */
    public function getOrder(): Order;

    /**
     * Sets the order to invoice
     *
     * @param Order $order
     *
     * @return InvoiceProcessItemInterface
     */
    public function setOrder(Order $order): InvoiceProcessItemInterface;

    /**
     * Returns the destination status
     *
     * @returns string
     */
    public function getDestinationStatus(): string;

    /**
     * Sets the destination status
     *
     * @param string $status
     *
     * @return InvoiceProcessItemInterface
     */
    public function setDestinationStatus(string $status): InvoiceProcessItemInterface;

    /**
     * Returns the capture mode
     *
     * @returns string
     */
    public function getCaptureMode(): string;

    /**
     * Sets the capture mode
     *
     * @param string $captureMode
     *
     * @return InvoiceProcessItemInterface
     */
    public function setCaptureMode(string $captureMode): InvoiceProcessItemInterface;

    /**
     * Returns the capture mode
     *
     * @returns string
     */
    public function getEmailCopyOfInvoice(): bool;

    /**
     * Sets the capture mode
     *
     * @param bool $bool
     *
     * @return InvoiceProcessItemInterface
     */
    public function setEmailCopyOfInvoice(bool $bool): InvoiceProcessItemInterface;
}