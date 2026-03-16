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
interface AttachmentInterface extends ApiObjectInterface
{
    /**
     * The content type of the attachment.
     *
     * @param string $type
     */
    public function setContentType($type);

    /**
     * The body of the attachment in serialized JSON.
     *
     * @param string $body
     */
    public function setBody($body);
}
