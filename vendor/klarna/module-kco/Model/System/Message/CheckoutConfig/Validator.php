<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\CheckoutConfig;

use Magento\Store\Model\StoreManagerInterface;
use Klarna\Kco\Model\Checkout\Configuration\SettingsProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Eav\Model\Config;
use Klarna\Base\Helper\VersionInfo;

/**
 * Checking checkout settings
 *
 * @internal
 */
class Validator
{

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    /** @var SettingsProvider $providerConfig */
    private $providerConfig;

    /** @var Config $config */
    private $config;

    /** @var VersionInfo $info */
    private $info;

    /**
     * @param StoreManagerInterface $storeManager
     * @param SettingsProvider $providerConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $config
     * @param VersionInfo $info
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SettingsProvider $providerConfig,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        VersionInfo $info
    ) {
        $this->storeManager = $storeManager;
        $this->providerConfig = $providerConfig;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->info = $info;
    }

    /**
     * Returns a list of stores where the title is incorrectly configured
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoresWhereTitleIncorrect()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);
        $titleShop = $this->isCustomerEavValueRequired('prefix');

        foreach ($storeCollection as $store) {
            $titleKlarna = $this->providerConfig->isCheckoutConfigFlag('title_mandatory', $store);
            if ($this->info->getMageEdition() === 'Community') {
                $titleShop = $this->getScopeConfigValue('prefix_show', $store);
            }

            if (!$this->settingsValid($titleKlarna, $titleShop)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns a list of stores where the date of birth is incorrectly configured
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoresWhereDateOfBirthIncorrect()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);
        $dobShop = $this->isCustomerEavValueRequired('dob');

        foreach ($storeCollection as $store) {
            $dobKlarna = $this->providerConfig->isCheckoutConfigFlag('dob_mandatory', $store);
            if ($this->info->getMageEdition() === 'Community') {
                $dobShop = $this->getScopeConfigValue('dob_show', $store);
            }

            if (!$this->settingsValid($dobKlarna, $dobShop)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns a list of stores where the phone is incorrectly configured
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoresWherePhoneIncorrect()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            $phoneKlarna = $this->providerConfig->isCheckoutConfigFlag('phone_mandatory', $store);
            $phoneShop = $this->getScopeConfigValue('telephone_show', $store);

            if (!$this->settingsValid($phoneKlarna, $phoneShop)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Getting back the scope config value
     *
     * @param string $key
     * @param StoreInterface|null $store
     * @return string
     */
    private function getScopeConfigValue($key, $store = null)
    {
        $scope = ($store === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        return $this->scopeConfig->getValue('customer/address/' . $key, $scope, $store);
    }

    /**
     * Getting back the customer eav value
     *
     * @param string $key
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isCustomerEavValueRequired($key)
    {
        $attribute = $this->config->getAttribute('customer', $key);
        return $attribute->getIsRequired();
    }

    /**
     * Returns true when settings between klarna and the shop are correct to each other
     *
     * @param bool $klarnaSetting
     * @param string $shopSetting
     * @return bool
     */
    private function settingsValid($klarnaSetting, $shopSetting)
    {
        if ($shopSetting === 'req' || $shopSetting === '1') {
            return $klarnaSetting;
        }

        return true;
    }

    /**
     * Returns a list of stores where the terms and conditions url is empty
     *
     * @return array
     */
    public function getStoresWhereTermsUrlEmpty(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if ($this->isTermsUrlEmpty($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns true when the terms and conditions url is empty
     *
     * @param StoreInterface $store
     * @return bool
     */
    private function isTermsUrlEmpty(StoreInterface $store): bool
    {
        $url = $this->scopeConfig->getValue(
            'checkout/klarna_kco/terms_url',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return empty($url);
    }
}
