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
interface QuoteAuthorizationTokenRepositoryInterface
{
    /**
     * Getting back the authorization token
     *
     * @param string $authorizationToken
     * @return QuoteInterface
     */
    public function getByAuthorizationToken(string $authorizationToken): QuoteInterface;
}
