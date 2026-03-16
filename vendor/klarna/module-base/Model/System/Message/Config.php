<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Message;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @internal
 */
class Config
{
    /**
     * klarna/api/debug
     */
    public const CONFIG_XML_PATH_KLARNA_DEBUG = 'klarna/api/debug';
    /**
     * klarna/api/test_mode
     */
    public const CONFIG_XML_PATH_KLARNA_TEST_MODE = 'klarna/api/test_mode';
    /**
     * General -> Store Information -> Country
     */
    public const CONFIG_XML_PATH_GENERAL_STORE_INFORMATION_COUNTRY = 'general/store_information/country_id';
    /**
     * General -> Region -> State Required
     */
    public const CONFIG_XML_PATH_GENERAL_STATE_OPTIONS = 'general/region/state_required';

    public const CONFIG_XML_PATH_KLARNA_KP_ENABLED = 'payment/klarna_kp/active';
    public const CONFIG_XML_PATH_KLARNA_KCO_ENABLED = 'payment/klarna_kco/active';
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @codeCoverageIgnore
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Check to see if any store or default has a Klarna payment method enabled
     *
     * @return bool
     */
    public function isKlarnaEnabledInAnyStore()
    {
        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if ($this->isAnyKlarnaProductEnabledForStore($store)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if KP or KCO are enabled in the target store
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isAnyKlarnaProductEnabledForStore(StoreInterface $store): bool
    {
        $kp = $this->scopeConfig->isSetFlag(
            self::CONFIG_XML_PATH_KLARNA_KP_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($kp) {
            return true;
        }

        return $this->scopeConfig->isSetFlag(
            self::CONFIG_XML_PATH_KLARNA_KCO_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function storeAddressSet($store = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_GENERAL_STORE_INFORMATION_COUNTRY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     *
     * @param   null|string|bool|int|Store $store
     * @return  bool
     */
    public function debugModeWhileLive($store = null)
    {
        if ($this->testMode($store)) {
            return false;
        }
        return $this->debugMode($store);
    }

    /**
     * Get defined tax calculation algorithm
     *
     * @param   null|string|bool|int|Store $store
     * @return  string
     */
    public function testMode($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_XML_PATH_KLARNA_TEST_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get tax class id specified for shipping tax estimation
     *
     * @param   null|string|bool|int|Store $store
     * @return  int
     */
    public function debugMode($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_XML_PATH_KLARNA_DEBUG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return a list of countries that incorrectly have the state/region marked as required
     *
     * @return array
     */
    public function requiredRegions()
    {
        $failed = [];
        $knownCountriesWithOptionalRegion = [
            'at',
            'de',
            'fi',
        ];
        $countries = $this->scopeConfig->getValue(self::CONFIG_XML_PATH_GENERAL_STATE_OPTIONS);
        if ($countries === null) {
            return $failed;
        }

        $countries = explode(',', $countries);
        foreach ($knownCountriesWithOptionalRegion as $country) {
            if (in_array($country, $countries)) {
                $failed[] = $country;
            }
        }
        return $failed;
    }
}
