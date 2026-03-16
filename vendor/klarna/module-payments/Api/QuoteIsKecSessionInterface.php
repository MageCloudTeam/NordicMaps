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
interface QuoteIsKecSessionInterface
{

    /**
     * Marking it as KEC session
     *
     * @return $this
     */
    public function markAsKecSession(): self;

    /**
     * Getting back the customer type
     *
     * @return bool
     */
    public function isKecSession(): bool;
}
