<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
declare(strict_types=1);

namespace MageCloud\EnhancedEcommerce\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;

/**
 * Class Data
 * @package MageCloud\EnhancedEcommerce\Helper
 */
class Data extends AbstractHelper
{
    /**
     * XML path
     *
     * general settings
     */
    const XML_PATH_GENERAL_ENABLED = 'magecloud_enhanced_ecommerce/general/enabled';
    const XML_PATH_GENERAL_GTM_CODE = 'magecloud_enhanced_ecommerce/general/gtm_code';
    const XML_PATH_GENERAL_GTM_NOSCRIPT_CODE = 'magecloud_enhanced_ecommerce/general/gtm_noscript_code';
    /**
     * product settings
     */
    const XML_PATH_PRODUCT_SETTINGS_PRODUCT_IDENTIFIER = 'magecloud_enhanced_ecommerce/product_settings/product_identifier';
    const XML_PATH_PRODUCT_SETTINGS_BRAND_ATTRIBUTE = 'magecloud_enhanced_ecommerce/product_settings/brand_attribute';
    /**
     * checkout settings
     */
    const XML_PATH_CHECKOUT_SETTINGS_ORDER_TOTAL = 'magecloud_enhanced_ecommerce/checkout_settings/order_total';
    const XML_PATH_CHECKOUT_SETTINGS_DEDUCT_TAX_FROM_GRAND_TOTAL = 'magecloud_enhanced_ecommerce/checkout_settings/deduct_tax_from_grand_total';
    const XML_PATH_CHECKOUT_SETTINGS_INCLUDE_TAX = 'magecloud_enhanced_ecommerce/checkout_settings/include_tax';
    const XML_PATH_CHECKOUT_SETTINGS_INCLUDE_SHIPPING = 'magecloud_enhanced_ecommerce/checkout_settings/include_shipping';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var Toolbar
     */
    private $toolbar;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     * @param Toolbar $toolbar
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver,
        Toolbar $toolbar
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->toolbar = $toolbar;
    }

    /**
     * @param $store
     * @return bool
     */
    public function isEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $store
     * @return mixed|null
     */
    public function getGtmCode($store = null)
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_GENERAL_GTM_CODE,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return '';
    }

    /**
     * @param $store
     * @return mixed|null
     */
    public function getGtmNoScriptCode($store = null)
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_GENERAL_GTM_NOSCRIPT_CODE,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return '';
    }

    /**
     * @param $store
     * @return mixed|null
     */
    public function getProductIdentifier($store = null)
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_PRODUCT_SETTINGS_PRODUCT_IDENTIFIER,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return null;
    }

    /**
     * @param $store
     * @return mixed|null
     */
    public function getBrandAttribute($store = null)
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_PRODUCT_SETTINGS_BRAND_ATTRIBUTE,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return null;
    }

    /**
     * @param $store
     * @return mixed|null
     */
    public function getOrderTotal($store = null)
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_CHECKOUT_SETTINGS_ORDER_TOTAL,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return null;
    }

    /**
     * @param $store
     * @return mixed|null
     */
    public function deductTaxFromGrandTotal($store = null)
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_CHECKOUT_SETTINGS_DEDUCT_TAX_FROM_GRAND_TOTAL,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return false;
    }



    /**
     * @param $store
     * @return bool
     */
    public function includeTax($store = null): bool
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->isSetFlag(
                self::XML_PATH_CHECKOUT_SETTINGS_INCLUDE_TAX,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return false;
    }

    /**
     * @param $store
     * @return bool
     */
    public function includeShipping($store = null): bool
    {
        if ($this->isEnabled($store)) {
            return $this->scopeConfig->isSetFlag(
                self::XML_PATH_CHECKOUT_SETTINGS_INCLUDE_SHIPPING,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return false;
    }

    /**
     * @return int
     */
    public function getCatalogListCurrentPage(): int
    {
        return $this->toolbar->getCurrentPage();
    }

    /**
     * @return int
     */
    public function getCatalogListCurrentLimit(): int
    {
        return (int)$this->toolbar->getLimit();
    }

    /**
     * @param $store
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCurrentCurrencyCode($store = null): string
    {
        return $this->storeManager->getStore($store)->getCurrentCurrencyCode();
    }
}