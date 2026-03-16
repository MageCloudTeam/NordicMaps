<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

/**
 * @api
 */
interface QuoteOrderIdInterface
{
    /**
     * Set the shop order_id
     *
     * @param string $orderId
     * @return self
     */
    public function setOrderId(string $orderId): self;

    /**
     * Get the shop order_id
     *
     * @return string
     */
    public function getOrderId(): ?string;
}
