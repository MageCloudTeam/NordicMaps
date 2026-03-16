<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Message\CountryConfig;

use Magento\Framework\Notification\MessageInterface;
use Klarna\Base\Model\System\Message\Config;

/**
 * Showing notifications based on country configurations
 *
 * @internal
 */
class Notification implements MessageInterface
{
    /** @var Validator $validator */
    private $validator;

    /** @var Config $klarnaConfig */
    private $klarnaConfig;

    /** @var array $storesFailedRegionCountries */
    private $storesFailedRegionCountries = [];

    /** @var array $storesWithoutCountry */
    private $storesWithoutCountry = [];

    /** @var Message $message */
    private $message;

    /**
     * @param Validator $validator
     * @param Config $klarnaConfig
     * @param Message $message
     * @codeCoverageIgnore
     */
    public function __construct(
        Validator $validator,
        Config $klarnaConfig,
        Message $message
    ) {
        $this->validator = $validator;
        $this->klarnaConfig = $klarnaConfig;
        $this->message = $message;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return hash('sha256', 'KLARNA_COUNTRY_CONFIG_NOTIFICATION');
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

        $this->storesFailedRegionCountries = $this->validator->getStoresWhereCountriesWithInvalidRegion();
        $this->storesWithoutCountry = $this->validator->getStoresWithoutCountryInStoreInformation();

        return !empty($this->storesFailedRegionCountries) ||
            !empty($this->storesWithoutCountry);
    }

    /**
     * Return the notification message
     *
     * @return string
     */
    public function getText()
    {
        $message = '';
        if (!empty($this->storesFailedRegionCountries)) {
            $message .= $this->message->getMessageFailedRegionCountries($this->storesFailedRegionCountries);
        }
        if (!empty($this->storesWithoutCountry)) {
            $message .= $this->message->getMessageNoStoreCountrySelected($this->storesWithoutCountry);
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
