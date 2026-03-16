<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Api\Request;

use Klarna\Kp\Api\Data\AttachmentInterface;

/**
 * @internal
 */
class Attachment implements AttachmentInterface
{
    use \Klarna\Kp\Model\Api\Export;

    /**
     * @var string
     */
    private $content_type;

    /**
     * @var string
     */
    private $body;

    /**
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->exports[] = $key;
            }
        }
    }

    /**
     * The content type of the attachment.
     *
     * @param string $type
     */
    public function setContentType($type)
    {
        $this->content_type = $type;
    }

    /**
     * The body of the attachment in serialized JSON.
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
