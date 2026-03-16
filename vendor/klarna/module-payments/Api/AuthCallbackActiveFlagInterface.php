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
interface AuthCallbackActiveFlagInterface
{
    /**
     * Set the authorization callback active flag
     *
     * @param bool $flag
     * @return self
     */
    public function setAuthCallbackActiveFlag(bool $flag): self;

    /**
     * Returns true if the authorization callback is active
     *
     * @return bool
     */
    public function isAuthCallbackActive(): bool;
}
