<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\AutoInvoice\Api;

use MageCloud\AutoInvoice\Api\Data\InvoiceProcessItemInterface;
use Magento\Sales\Model\Order;

interface InvoiceProcessInterface
{
    /**
     * Returns item to process.
     * Item consists of an order, and a destination status.
     *
     * @param Order $order
     *
     * @return InvoiceProcessItemInterface|null
     */
    public function getItemToProcess(Order $order): ?InvoiceProcessItemInterface;

    /**
     * Invoice order
     *
     * @param InvoiceProcessItemInterface $item
     */
    public function invoice(InvoiceProcessItemInterface $item): void;
}