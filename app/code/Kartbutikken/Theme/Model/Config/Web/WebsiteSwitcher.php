<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Model\Config\Web;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class WebsiteSwitcher
 */
class WebsiteSwitcher
{
    /**
     * Configuration paths
     */
    const XML_CONFIG_ENABLED = 'web/website_switcher/enabled';
    const XML_CONFIG_SHOW_ALL_WEBSITES = 'web/website_switcher/show_all_websites';
    const XML_CONFIG_CUSTOM_WEBSITES = 'web/website_switcher/custom_websites';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * WebsiteSwitcher constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string
     */
    public function isEnabled(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ENABLED, $scopeType, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return bool
     */
    public function isShowAllWebsites(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_SHOW_ALL_WEBSITES, $scopeType, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return array
     */
    public function getCustomWebsites(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): array
    {
        return array_filter(
            explode(
                ',',
                $this->scopeConfig->getValue(self::XML_CONFIG_CUSTOM_WEBSITES, $scopeType, $scopeCode) ?? ''
            )
        );
    }
}