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
interface UrlsInterface extends ApiObjectInterface
{
    /**
     * URL of merchant confirmation page.
     *
     * @param string $comfirmation
     */
    public function setConfirmation($comfirmation);

    /**
     * URL for notifications on pending orders. (max 2000 characters)
     *
     * @param string $notification
     */
    public function setNotification($notification);
}
