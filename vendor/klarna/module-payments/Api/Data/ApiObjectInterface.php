<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kp\Api\Data;

/**
 * @api
 */
interface ApiObjectInterface
{
    /**
     * Generate array object needed for API call
     *
     * @return array
     */
    public function toArray();
}
