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
interface KlarnaPaymentMethodInterface
{
    /**
     * Set payment method code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);
}
