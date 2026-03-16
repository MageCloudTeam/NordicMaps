<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\Configuration;

use Klarna\Base\Model\Configuration\Api;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class ApiValidation
{
    /**
     * @var array
     */
    private array $failedValidationHistory = [];
    /**
     * @var Payment
     */
    private Payment $paymentConfig;
    /**
     * @var Api
     */
    private Api $apiConfiguration;

    /**
     * @param Payment $paymentConfig
     * @param Api $apiConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(Payment $paymentConfig, Api $apiConfiguration)
    {
        $this->paymentConfig = $paymentConfig;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * Returns true if KP is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKpEnabled(StoreInterface $store): bool
    {
        $result = $this->paymentConfig->isKpEnabled($store);
        if (!$result) {
            $this->failedValidationHistory[] = 'Klarna Payments in not enabled';
        }

        return $result;
    }

    /**
     * Returns true if the KP endpoint is selected
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKpEndpointSelected(StoreInterface $store): bool
    {
        $apiVersion = $this->apiConfiguration->getEndpoint($store);
        if (!$apiVersion) {
            $this->failedValidationHistory[] = 'No Klarna Payments endpoint is selected';
            return false;
        }

        $result = substr($apiVersion, 0, 3) === 'kp_';
        if (!$result) {
            $this->failedValidationHistory[] = 'No Klarna Payments endpoint is selected';
        }

        return $result;
    }

    /**
     * Returns true if the KP endpoint for the US market is selected
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKpEndpointSelectedForUsMarket(StoreInterface $store): bool
    {
        if (!$this->isKpEndpointSelected($store)) {
            return false;
        }

        $apiVersion = $this->apiConfiguration->getEndpoint($store);
        if ($apiVersion !== 'kp_na') {
            $this->failedValidationHistory[] = 'The US endpoint is not selected';
            return false;
        }

        return true;
    }

    /**
     * Returns true if the KP api request is allowed to be sent
     *
     * @param CartInterface $quote
     * @return bool
     */
    public function sendApiRequestAllowed(CartInterface $quote): bool
    {
        $store = $quote->getStore();
        if (!$this->isKpEndpointSelected($store) || !$this->isKpEnabled($store)) {
            return false;
        }

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $quoteCountry = $address->getCountryId();

        if (!$quoteCountry) {
            return true;
        }

        $kpCountries = $this->paymentConfig->getAllowedCountries($store);
        if (empty($kpCountries)) {
            return true;
        }

        $result = in_array($quoteCountry, $kpCountries);
        if (!$result) {
            $this->failedValidationHistory[] =
                'Klarna Payments is not allowed to be shown for quote id: ' . $quote->getId();
        }

        return $result;
    }

    /**
     * Getting back the failed validation history
     *
     * @return array
     */
    public function getFailedValidationHistory(): array
    {
        return $this->failedValidationHistory;
    }

    /**
     * Clearing the failed validation history
     */
    public function clearFailedValidationHistory():void
    {
        $this->failedValidationHistory = [];
    }
}
