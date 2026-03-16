<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Model;

use MageCloud\AutoInvoice\Api\Data\InvoiceProcessItemInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order;

/**
 * Class InvoiceProcessItem
 */
class InvoiceProcessItem extends DataObject implements InvoiceProcessItemInterface
{
    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->_getData(self::KEY_ORDER);
    }

    /**
     * @param Order $order
     *
     * @return $this|InvoiceProcessItemInterface
     */
    public function setOrder(Order $order): InvoiceProcessItemInterface
    {
        $this->setData(self::KEY_ORDER, $order);

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationStatus(): string
    {
        return $this->_getData(self::KEY_DESTINATION_STATUS);
    }

    /**
     * @param string $status
     *
     * @return $this|InvoiceProcessItemInterface
     */
    public function setDestinationStatus(string $status): InvoiceProcessItemInterface
    {
        $this->setData(self::KEY_DESTINATION_STATUS, $status);

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptureMode(): string
    {
        return $this->_getData(self::KEY_CAPTURE_MODE);
    }

    /**
     * @param string $captureMode
     *
     * @return $this|InvoiceProcessItemInterface
     */
    public function setCaptureMode(string $captureMode): InvoiceProcessItemInterface
    {
        $this->setData(self::KEY_CAPTURE_MODE, $captureMode);

        return $this;
    }

    /**
     * @return bool
     */
    public function getEmailCopyOfInvoice(): bool
    {
        return $this->_getData(self::KEY_EMAIL_COPY_OF_INVOICE);
    }

    /**
     * @param bool $bool
     *
     * @return InvoiceProcessItemInterface
     */
    public function setEmailCopyOfInvoice(bool $bool): InvoiceProcessItemInterface
    {
        $this->setData(self::KEY_EMAIL_COPY_OF_INVOICE, $bool);

        return $this;
    }
}