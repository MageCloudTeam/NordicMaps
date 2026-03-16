<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\System\Message\DesignConfig;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Validating different Checkout design values
 *
 * @internal
 */
class Validator
{

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var ScopeConfigInterface $scopeConfig */
    private $scopeConfig;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns a list of stores where the link color is invalid configured
     *
     * @return array
     */
    public function getStoresWhereLinkColorInvalid(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if (!$this->isValidCssHexColorCode($store, 'checkout/klarna_kco_design/color_link')) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns a list of stores where the color header is invalid configured
     *
     * @return array
     */
    public function getStoresWhereHeaderColorInvalid(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if (!$this->isValidCssHexColorCode($store, 'checkout/klarna_kco_design/color_header')) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns a list of stores where the checkbox checkmark color is invalid configured
     *
     * @return array
     */
    public function getStoresWhereCheckboxCheckmarkColorInvalid(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if (!$this->isValidCssHexColorCode($store, 'checkout/klarna_kco_design/color_checkbox_checkmark')) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns a list of stores where the checkbox color is invalid configured
     *
     * @return array
     */
    public function getStoresWhereCheckboxColorInvalid(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if (!$this->isValidCssHexColorCode($store, 'checkout/klarna_kco_design/color_checkbox')) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns a list of stores where the button text color is invalid configured
     *
     * @return array
     */
    public function getStoresWhereButtonTextColorInvalid(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if (!$this->isValidCssHexColorCode($store, 'checkout/klarna_kco_design/color_button_text')) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns a list of stores where the button color is invalid configured
     *
     * @return array
     */
    public function getStoresWhereButtonColorInvalid(): array
    {
        $storeNames = [];
        $storeCollection = $this->storeManager->getStores(true);

        foreach ($storeCollection as $store) {
            if (!$this->isValidCssHexColorCode($store, 'checkout/klarna_kco_design/color_button')) {
                $website = $store->getWebsite();
                $storeNames[] = $website->getName() . '(' . $store->getName() . ')';
            }
        }

        return $storeNames;
    }

    /**
     * Returns true when for the given key the database entry is a css hex color code
     *
     * @param StoreInterface $store
     * @param string $databaseKey
     * @return bool
     */
    private function isValidCssHexColorCode(StoreInterface $store, $databaseKey): bool
    {
        $color = $this->scopeConfig->getValue(
            $databaseKey,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($color)) {
            return true;
        }

        return preg_match('/^#[A-Fa-f0-9]{6}$/', $color) > 0;
    }
}
