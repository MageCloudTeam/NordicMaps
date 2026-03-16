<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\DesignConfig;

use Magento\Framework\Notification\MessageInterface;
use Klarna\Base\Model\System\Message\Config;

/**
 * Showing notifications based on kco design configurations
 *
 * @internal
 */
class Notification implements MessageInterface
{
    /** @var Config $klarnaConfig */
    private $klarnaConfig;

    /** @var Validator $validator */
    private $validator;

    /** @var Message $message */
    private $message;

    /** @var array $storesInvalidLinkColor */
    private $storesInvalidLinkColor = [];

    /** @var array $storesInvalidHeaderColor */
    private $storesInvalidHeaderColor = [];

    /** @var array $storesInvalidCheckboxCheckmarkColor */
    private $storesInvalidCheckboxCheckmarkColor = [];

    /** @var array $storesInvalidCheckboxColor */
    private $storesInvalidCheckboxColor = [];

    /** @var array $storesInvalidButtonTextColor */
    private $storesInvalidButtonTextColor = [];

    /** @var array $storesInvalidButtonColor */
    private $storesInvalidButtonColor = [];

    /**
     * @param Config $klarnaConfig
     * @param Validator $validator
     * @param Message $message
     * @codeCoverageIgnore
     */
    public function __construct(
        Config $klarnaConfig,
        Validator $validator,
        Message $message
    ) {
        $this->klarnaConfig = $klarnaConfig;
        $this->validator = $validator;
        $this->message = $message;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity(): string
    {
        return hash('sha256', 'KLARNA_KCO_DESIGN_CONFIG_NOTIFICATION');
    }

    /**
     * Checks if we will show a notification message
     *
     * @return bool
     */
    public function isDisplayed(): bool
    {
        if (!$this->klarnaConfig->isKlarnaEnabledInAnyStore()) {
            return false;
        }

        $this->storesInvalidButtonTextColor = $this->validator->getStoresWhereButtonTextColorInvalid();
        $this->storesInvalidButtonColor = $this->validator->getStoresWhereButtonColorInvalid();
        $this->storesInvalidCheckboxColor = $this->validator->getStoresWhereCheckboxColorInvalid();
        $this->storesInvalidCheckboxCheckmarkColor = $this->validator->getStoresWhereCheckboxCheckmarkColorInvalid();
        $this->storesInvalidHeaderColor = $this->validator->getStoresWhereHeaderColorInvalid();
        $this->storesInvalidLinkColor = $this->validator->getStoresWhereLinkColorInvalid();

        return !empty($this->storesInvalidButtonTextColor) ||
            !empty($this->storesInvalidButtonColor) ||
            !empty($this->storesInvalidCheckboxColor) ||
            !empty($this->storesInvalidCheckboxCheckmarkColor) ||
            !empty($this->storesInvalidHeaderColor) ||
            !empty($this->storesInvalidLinkColor);
    }

    /**
     * Return the notification message
     *
     * @return string
     */
    public function getText(): string
    {
        $message = '';
        if (!empty($this->storesInvalidButtonTextColor)) {
            $message .= $this->message->getMessageInvalidButtonTextColor($this->storesInvalidButtonTextColor);
        }
        if (!empty($this->storesInvalidButtonColor)) {
            $message .= $this->message->getMessageInvalidButtonColor($this->storesInvalidButtonColor);
        }
        if (!empty($this->storesInvalidCheckboxColor)) {
            $message .= $this->message->getMessageInvalidCheckboxColor($this->storesInvalidCheckboxColor);
        }
        if (!empty($this->storesInvalidCheckboxCheckmarkColor)) {
            $message .= $this->message->getMessageInvalidCheckboxCheckmarkColor(
                $this->storesInvalidCheckboxCheckmarkColor
            );
        }
        if (!empty($this->storesInvalidHeaderColor)) {
            $message .= $this->message->getMessageInvalidHeaderColor($this->storesInvalidHeaderColor);
        }
        if (!empty($this->storesInvalidLinkColor)) {
            $message .= $this->message->getMessageInvalidLinkColor($this->storesInvalidLinkColor);
        }

        return $message;
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity(): int
    {
        return self::SEVERITY_CRITICAL;
    }
}
