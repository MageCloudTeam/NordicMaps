<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\ApiConfig;

use Klarna\Kco\Model\Payment\Kco;
use Magento\Store\Model\StoreManagerInterface;
use Klarna\Base\Model\System\Message\Config;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Kco\Model\Checkout\Configuration\SettingsProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Klarna\Base\Helper\KlarnaConfig;

/**
 * Checking api config settings
 *
 * @internal
 */
class Validator
{

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var SettingsProvider $providerConfig */
    private $providerConfig;

    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    /** @var KlarnaConfig $klarnaConfig */
    private $klarnaConfig;

    /** @var Config $config */
    private $config;

    /**
     * @param StoreManagerInterface $storeManager
     * @param SettingsProvider $providerConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param KlarnaConfig $klarnaConfig
     * @param Config $config
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SettingsProvider $providerConfig,
        ScopeConfigInterface $scopeConfig,
        KlarnaConfig $klarnaConfig,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->providerConfig = $providerConfig;
        $this->scopeConfig = $scopeConfig;
        $this->klarnaConfig = $klarnaConfig;
        $this->config = $config;
    }

    /**
     * Performing all validations and returning the result
     *
     * @return array
     * @throws \Klarna\Base\Exception
     */
    public function getStoresWhereValidationFails()
    {
        return [
            'kp_kco_enabled' => $this->getStoresWhereKpKcoEnabled(),
            'api_not_kco' => $this->getStoresWhereApiNotKco(),
            'merchant_id_empty' => $this->getStoresWhereMerchantIdIsEmpty(),
            'password_empty' => $this->getStoresWherePasswordIsEmpty()
        ];
    }

    /**
     * Returns a list of stores where kp and kco is enabled
     *
     * @return array
     */
    private function getStoresWhereKpKcoEnabled()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if ($this->providerConfig->isKlarnaCheckoutPaymentEnabled($store) && $this->isKpEnabled($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns true when kp is enabled
     *
     * @param StoreInterface|null $store
     * @return bool
     */
    private function isKpEnabled($store = null)
    {
        $scope = ($store === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        return $this->scopeConfig->isSetFlag('payment/' . Kco::METHOD_CODE . '/active', $scope, $store);
    }

    /**
     * Returns a list of stores where kco is enabled but the api version is not kco
     *
     * @return array
     * @throws \Klarna\Base\Exception
     */
    private function getStoresWhereApiNotKco()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if ($this->providerConfig->isKlarnaCheckoutPaymentEnabled($store) && !$this->isKcoApiConfigured($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns true when the kco api is configured
     *
     * @param StoreInterface|null $store
     * @return bool
     * @throws \Klarna\Base\Exception
     */
    private function isKcoApiConfigured($store = null)
    {
        $config = $this->klarnaConfig->getVersionConfig($store);

        $invalidApi = ['kp_na', 'kp_eu', ''];
        return !in_array($config->getCode(), $invalidApi);
    }

    /**
     * Returns a list of stores where kp or kco is enabled but the merchant id field is empty
     *
     * @return array
     */
    private function getStoresWhereMerchantIdIsEmpty(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if ($this->isKpOrKcoEnabled($store) && $this->isMerchantIdEmpty($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns true when kp or kco is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    private function isKpOrKcoEnabled(StoreInterface $store): bool
    {
        return $this->isKpEnabled($store) || $this->providerConfig->isKlarnaCheckoutPaymentEnabled($store);
    }

    /**
     * Returns true when the merchant id is empty
     *
     * @param StoreInterface $store
     * @return bool
     */
    private function isMerchantIdEmpty(StoreInterface $store): bool
    {
        $scope = $this->getScope($store);
        $merchantId = $this->scopeConfig->getValue('klarna/api/merchant_id', $scope, $store);

        return empty($merchantId);
    }

    /**
     * Returns a list of stores where kp or kco is enabled but the password field is empty
     *
     * @return array
     */
    private function getStoresWherePasswordIsEmpty(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if ($this->isKpOrKcoEnabled($store) && $this->isPasswordEmpty($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns true when the password is empty
     *
     * @param StoreInterface $store
     * @return bool
     */
    private function isPasswordEmpty(StoreInterface $store = null): bool
    {
        $scope = $this->getScope($store);
        $password = $this->scopeConfig->getValue('klarna/api/shared_secret', $scope, $store);

        return empty($password);
    }

    /**
     * Get the scope value of the store
     *
     * @param StoreInterface $store
     * @return string
     */
    private function getScope(StoreInterface $store = null)
    {
        if ($store === null) {
            return ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }
        return ScopeInterface::SCOPE_STORES;
    }
}
