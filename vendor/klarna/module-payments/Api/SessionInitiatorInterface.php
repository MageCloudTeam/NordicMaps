<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api;

use Magento\Quote\Api\Data\CartInterface;

/**
 * @api
 */
interface SessionInitiatorInterface
{

    /**
     * Checking the availability
     *
     * @param CartInterface $quote
     * @param string $code
     * @return bool
     */
    public function checkAvailable(CartInterface $quote, string $code);
}
