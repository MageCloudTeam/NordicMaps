<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\TargetConfig;

use Klarna\Kco\Model\System\Config\Source\Customer\Group;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Checking target configurations
 *
 * @internal
 */
class Validator
{

    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var Group $group */
    private $group;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Group                 $group
     * @codeCoverageIgnore
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Group $group
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->group = $group;
    }

    /**
     * Getting back the stores where no shipping countries are enabled
     *
     * @return array
     */
    public function getStoresWithoutEnabledShippingCountries(): array
    {
        $result = [];

        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if (!$this->isAnyShippingCountryEnabled($store)) {
                $website = $store->getWebsite();
                $result[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $result;
    }

    /**
     * Returns true when at least one shipping country is enabled
     *
     * @param StoreInterface $store
     * @return bool
     */
    private function isAnyShippingCountryEnabled(StoreInterface $store): bool
    {
        $countries = $this->scopeConfig->getValue(
            'payment/klarna_kco/shipping_countries',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $countries !== '';
    }

    /**
     * Getting back the stores where no customer groups are enabled
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoresWithoutEnabledCustomerGroups(): array
    {
        $result = [];

        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if (!$this->isAnyCustomerGroupEnabled($store)) {
                $website = $store->getWebsite();
                $result[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $result;
    }

    /**
     * Returns true when at least one customer group is enabled
     *
     * @param StoreInterface $store
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isAnyCustomerGroupEnabled(StoreInterface $store): bool
    {
        $disabledGroups = $this->scopeConfig->getValue(
            'payment/klarna_kco/disable_customer_group',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($disabledGroups)) {
            return true;
        }

        $disabledGroupsArray = explode(',', $disabledGroups);
        $existingGroups = $this->group->getAllOptions(false);

        foreach ($existingGroups as $group) {
            if ($group['value'] === -1) {
                continue;
            }
            if (!in_array($group['value'], $disabledGroupsArray)) {
                return true;
            }
        }

        return false;
    }
}
