<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Kss\Api;

use \Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface DeliveryDetailsInterface
{
    /**
     * Returns the TMS delivery data for a given Klarna session id
     *
     * @param string $sessionId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDeliveryDataByKlarnaSessionId(string $sessionId): ?string;
}
