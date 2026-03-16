<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\ApiConfig;

use Klarna\Base\Exception;
use Magento\Framework\Notification\MessageInterface;
use Klarna\Base\Model\System\Message\Config;

/**
 * Showing notifications based on api configurations
 *
 * @internal
 */
class Notification implements MessageInterface
{
    /** @var Config $klarnaConfig */
    private $klarnaConfig;

    /** @var Validator $validator */
    private $validator;

    /** @var array $validationResult */
    private $validationResult = [];

    /** @var Message $message */
    private $message;

    /**
     * @param Config           $klarnaConfig
     * @param Validator        $validator
     * @param Message          $message
     * @codeCoverageIgnore
     */
    public function __construct(
        Config $klarnaConfig,
        Validator $validator,
        Message $message
    ) {
        $this->klarnaConfig = $klarnaConfig;
        $this->validator    = $validator;
        $this->message      = $message;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return hash('sha256', 'KLARNA_KCO_API_CONFIG_NOTIFICATION');
    }

    /**
     * Checks if we will show a notification message
     *
     * @return bool
     * @throws Exception
     */
    public function isDisplayed()
    {
        if (!$this->klarnaConfig->isKlarnaEnabledInAnyStore()) {
            return false;
        }

        $this->validationResult = $this->validator->getStoresWhereValidationFails();
        foreach ($this->validationResult as $result) {
            if (!empty($result)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the notification message
     *
     * @return string
     */
    public function getText()
    {
        return $this->message->getMessages($this->validationResult);
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
