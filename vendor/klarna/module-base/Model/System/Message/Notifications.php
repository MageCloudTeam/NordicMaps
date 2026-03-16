<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\System\Message;

use Klarna\Base\Model\System\Message\Config;
use Magento\Framework\DataObject\IdentityService;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @internal
 */
class Notifications implements MessageInterface
{
    /**
     * Store manager object
     *
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;
    /**
     * Klarna configuration object
     *
     * @var Config
     */
    private $klarnaConfig;
    /**
     * List of countries where region is marked as required and should not be
     *
     * @var array
     */
    private $regionRequired;
    /**
     * @var IdentityService
     */
    private $identityService;
    /**
     * @var array
     */
    private $storesWithMissingAddressSettings;
    /**
     * @var array
     */
    private $storesWithDebugWhileLiveSettings;

    /**
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface          $urlBuilder
     * @param Config                $klarnaConfig
     * @param IdentityService       $identityService
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        Config $klarnaConfig,
        IdentityService $identityService
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->klarnaConfig = $klarnaConfig;
        $this->identityService = $identityService;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->identityService->generateIdForData('KLARNA_ADDRESS_DEBUG_CONFIG_NOTIFICATION');
    }

    /**
     * Check whether notification is displayed
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (!$this->isKlarnaEnabled()) {
            return false;
        }

        $this->storesWithMissingAddressSettings = $this->getStoresWithMissingAddressSettings();
        $this->storesWithDebugWhileLiveSettings = $this->getStoresWithDebugWhileLiveSettings();
        $this->regionRequired = $this->getRegionRequired();

        // Check if we have valid Klarna notifications
        return (!empty($this->storesWithMissingAddressSettings))
            || (!empty($this->storesWithDebugWhileLiveSettings))
            || (!empty($this->regionRequired));
    }

    /**
     * Check to see if for any store or default a Klarna Product is enabled
     *
     * @return bool
     */
    private function isKlarnaEnabled()
    {
        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if ($this->klarnaConfig->isAnyKlarnaProductEnabledForStore($store)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Getting back stores with missing address settings
     *
     * @return array
     */
    public function getStoresWithMissingAddressSettings()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if (!$this->checkAddressSettings($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }
        return $storeNames;
    }

    /**
     * Checking the address settings
     *
     * @param \Magento\Store\Model\Store $store $store
     * @return bool
     */
    public function checkAddressSettings($store = null)
    {
        return $this->klarnaConfig->storeAddressSet($store);
    }

    /**
     * Getting back stores with debug mode enabled on production API environment
     *
     * @return array
     */
    public function getStoresWithDebugWhileLiveSettings()
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);
        foreach ($storeCollection as $store) {
            if ($this->checkDebugSettings($store)) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }
        return $storeNames;
    }

    /**
     * Checking debug settings
     *
     * @param \Magento\Store\Model\Store $store $store
     * @return bool
     */
    public function checkDebugSettings($store = null)
    {
        return $this->klarnaConfig->debugModeWhileLive($store);
    }

    /**
     * Getting back the required regions
     *
     * @return array
     */
    public function getRegionRequired()
    {
        return $this->klarnaConfig->requiredRegions();
    }

    /**
     * Build message text
     *
     * Determine which notification and data to display
     *
     * @return string
     */
    public function getText()
    {
        $messageDetails = '';

        if (!empty($this->storesWithMissingAddressSettings)) {
            $messageDetails .= '<strong>';
            $messageDetails .= __('Warning store address has not been set under Store Information.');
            $messageDetails .= '</strong><p>';
            $messageDetails .= __('Store(s) affected: ');
            $messageDetails .= implode(', ', $this->storesWithMissingAddressSettings);
            $messageDetails .= '</p><p>';
            $messageDetails .= __(
                'Click here to go to <a href="%1">General Configuration</a> and change your settings.',
                $this->getManageGeneralUrl()
            );
            $messageDetails .= "</p>";
        }

        if (!empty($this->storesWithDebugWhileLiveSettings)) {
            $messageDetails .= '<strong>';
            $messageDetails .= __(
                'Warning debug mode should only be enabled when test mode is active'
            );
            $messageDetails .= '</strong><p>';
            $messageDetails .= __('Store(s) affected: ');
            $messageDetails .= implode(', ', $this->storesWithDebugWhileLiveSettings);
            $messageDetails .= '</p><p>';
            $messageDetails .= __(
                'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
                $this->getManageKlarnaUrl()
            );
            $messageDetails .= "</p>";
        }

        if (!empty($this->regionRequired)) {
            $messageDetails .= '<strong>';
            $messageDetails .= __(
                'Warning the following countries are configured to require a region'
            );
            $messageDetails .= '</strong><p>';
            $messageDetails .= implode(', ', $this->regionRequired);
            $messageDetails .= '</p><p>';
            $messageDetails .= __(
                'Click here to go to <a href="%1">Klarna Configuration</a> and change your settings.',
                $this->getManageRegionsUrl()
            );
            $messageDetails .= "</p>";
        }

        return $messageDetails;
    }

    /**
     * Get URL to the admin General configuration page
     *
     * @return string
     */
    public function getManageGeneralUrl()
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/store_information');
    }

    /**
     * Get URL to the admin Klarna configuration page
     *
     * @return string
     */
    public function getManageKlarnaUrl()
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/klarna');
    }

    /**
     * Getting back the regions url
     *
     * @return string
     */
    public function getManageRegionsUrl()
    {
        return $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/general');
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
