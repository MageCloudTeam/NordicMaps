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
interface AuthorizationCallbackStatusInterface
{
    public const FAILED = -1;

    public const NOT_STARTED = 0;

    public const IN_PROGRESS = 1;

    public const SUCCESSFUL = 2;

    /**
     * Set the authorization callback active flag
     *
     * @param int $status
     * @return self
     */
    public function setAuthCallbackActiveCurrentStatus(int $status): self;

    /**
     * Returns true if the authorization callback is failed or not started yet
     *
     * @return bool
     */
    public function isAuthCallbackFailedOrNotStarted(): bool;

    /**
     * Returns true if the authorization callback is In progress or Successful false otherwise
     *
     * @return bool
     */
    public function isAuthCallbackAlreadyProcessed(): bool;

    /**
     * Returns true if the authorization callback is In progress
     *
     * @return bool
     */
    public function isAuthCallbackInProgress(): bool;
}
