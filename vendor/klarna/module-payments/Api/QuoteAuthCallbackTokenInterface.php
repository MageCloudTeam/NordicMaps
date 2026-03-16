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
interface QuoteAuthCallbackTokenInterface
{
    /**
     * Set the shop auth_callback_token
     *
     * @param string $token
     * @return self
     */
    public function setAuthTokenCallbackToken(string $token): self;

    /**
     * Get the shop auth_callback_token
     *
     * @return string
     */
    public function getAuthTokenCallbackToken(): ?string;
}
