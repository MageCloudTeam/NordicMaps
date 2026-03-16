<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Message\CountryConfig;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Checking country settings
 *
 * @internal
 */
class Validator
{
    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @codeCoverageIgnore
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Getting back stores where the countries are incorrectly have the state/region marked as required
     *
     * @return array
     */
    public function getStoresWhereCountriesWithInvalidRegion()
    {
        $result = [];

        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            $countries = $this->getCountries($store);
            $failed = array_filter(['AT', 'DE', 'FI'], function ($country) use ($countries) {
                return in_array($country, $countries);
            });

            if (!empty($failed)) {
                $countriesFlat = implode(', ', array_values($failed));
                $website = $store->getWebsite();
                $result[] = $website->getName() . '(' . $store->getName() . ')' . ': ' . $countriesFlat;
            }
        }

        return $result;
    }

    /**
     * Getting back a list of countries
     *
     * @param StoreInterface $store
     * @return array
     */
    private function getCountries($store = null)
    {
        $countries = $this->scopeConfig->getValue(
            'general/region/state_required',
            ScopeInterface::SCOPE_STORE,
            $store
        );
        if ($countries === null) {
            return [];
        }
        return explode(',', $countries);
    }

    /**
     * Getting back stores where no country is setup in the store information
     *
     * @return array
     */
    public function getStoresWithoutCountryInStoreInformation()
    {
        $result = [];

        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if (!$this->hasStoreCountryConfigured($store)) {
                $website = $store->getWebsite();
                $result[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $result;
    }

    /**
     * Returns true if the country is configured for the store
     *
     * @param StoreInterface|null $store
     * @return bool
     */
    private function hasStoreCountryConfigured($store = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'general/store_information/country_id',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
