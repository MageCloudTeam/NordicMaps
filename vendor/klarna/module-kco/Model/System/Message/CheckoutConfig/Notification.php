<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\CheckoutConfig;

use Magento\Framework\Notification\MessageInterface;
use Klarna\Base\Model\System\Message\Config;

/**
 * Showing notifications based on checkout configurations
 *
 * @internal
 */
class Notification implements MessageInterface
{
    /** @var Config $klarnaConfig */
    private $klarnaConfig;

    /** @var Validator $validator */
    private $validator;

    /** @var array $storesInvalidTitle */
    private $storesInvalidTitle = [];

    /** @var array $storesInvalidDob */
    private $storesInvalidDob = [];

    /** @var array $storesInvalidPhone */
    private $storesInvalidPhone = [];

    /** @var array $storesInvalidTermsUrl */
    private $storesInvalidTermsUrl = [];

    /** @var Message $message */
    private $message;

    /**
     * @param Config      $klarnaConfig
     * @param Validator   $validator
     * @param Message     $message
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
        return hash('sha256', 'KLARNA_KCO_CHECKOUT_CONFIG_NOTIFICATION');
    }

    /**
     * Checks if we will show a notification message
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (!$this->klarnaConfig->isKlarnaEnabledInAnyStore()) {
            return false;
        }

        $this->storesInvalidDob = $this->validator->getStoresWhereDateOfBirthIncorrect();
        $this->storesInvalidPhone = $this->validator->getStoresWherePhoneIncorrect();
        $this->storesInvalidTitle = $this->validator->getStoresWhereTitleIncorrect();
        $this->storesInvalidTermsUrl = $this->validator->getStoresWhereTermsUrlEmpty();

        return !empty($this->storesInvalidDob) ||
            !empty($this->storesInvalidPhone) ||
            !empty($this->storesInvalidTitle) ||
            !empty($this->storesInvalidTermsUrl);
    }

    /**
     * Return the notification message
     *
     * @return string
     */
    public function getText()
    {
        $message = '';
        if (!empty($this->storesInvalidTitle)) {
            $message .= $this->message->getMessageInvalidTitle($this->storesInvalidTitle);
        }
        if (!empty($this->storesInvalidPhone)) {
            $message .= $this->message->getMessageInvalidPhone($this->storesInvalidPhone);
        }
        if (!empty($this->storesInvalidDob)) {
            $message .= $this->message->getMessageInvalidDateOfBirth($this->storesInvalidDob);
        }
        if (!empty($this->storesInvalidTermsUrl)) {
            $message .= $this->message->getMessageInvalidTermsUrl($this->storesInvalidTermsUrl);
        }

        return $message;
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
