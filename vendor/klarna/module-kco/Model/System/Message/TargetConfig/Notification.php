<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\TargetConfig;

use Klarna\Base\Model\System\Message\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Notification\MessageInterface;

/**
 * Showing notifications based on target configurations
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

    /** @var array $storesInvalidShippingCountries */
    private $storesInvalidShippingCountries = [];

    /** @var array $storesInvalidCustomerGroups */
    private $storesInvalidCustomerGroups = [];

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
        return hash('sha256', 'KLARNA_KCO_TARGET_CONFIG_NOTIFICATION');
    }

    /**
     * Checks if we will show a notification message
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isDisplayed(): bool
    {
        if (!$this->klarnaConfig->isKlarnaEnabledInAnyStore()) {
            return false;
        }

        $this->storesInvalidShippingCountries = $this->validator->getStoresWithoutEnabledShippingCountries();
        $this->storesInvalidCustomerGroups = $this->validator->getStoresWithoutEnabledCustomerGroups();

        return !empty($this->storesInvalidShippingCountries) ||
            !empty($this->storesInvalidCustomerGroups);
    }

    /**
     * Return the notification message
     *
     * @return string
     */
    public function getText(): string
    {
        $message = '';
        if (!empty($this->storesInvalidShippingCountries)) {
            $message .= $this->message->getMessageInvalidShippingCountries($this->storesInvalidShippingCountries);
        }
        if (!empty($this->storesInvalidCustomerGroups)) {
            $message .= $this->message->getMessageInvalidCustomerGroups($this->storesInvalidCustomerGroups);
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
