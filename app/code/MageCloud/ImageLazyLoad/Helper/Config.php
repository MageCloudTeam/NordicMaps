<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ImageLazyLoad\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config extends AbstractHelper
{
    /**
     * Configuration paths
     */
    const XML_CONFIG_ENABLE = 'lazyload/general/enable';
    const XML_CONFIG_SKIP_CLASSES = 'lazyload/general/skip_classes';
    const XML_CONFIG_THRESHOLD = 'lazyload/general/threshold';
    const XML_CONFIG_DELAY = 'lazyload/general/delay';
    const XML_CONFIG_EFFECT = 'lazyload/general/effect';
    const XML_CONFIG_EFFECT_TIME = 'lazyload/general/effect_time';

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     *
     * @return bool
     */
    public function hasModuleEnabled(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        string $scopeCode = null
    ): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ENABLE, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function getSkipClasses(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        string $scopeCode = null
    ): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONFIG_SKIP_CLASSES, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     *
     * @return int
     */
    public function getThreshold(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        string $scopeCode = null
    ): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_CONFIG_THRESHOLD, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     *
     * @return int
     */
    public function getDelay(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        string $scopeCode = null
    ): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_CONFIG_DELAY, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function getEffect(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        string $scopeCode = null
    ): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_CONFIG_EFFECT, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     *
     * @return int
     */
    public function getEffectTime(
        string $scopeType = ScopeInterface::SCOPE_WEBSITE,
        string $scopeCode = null
    ): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_CONFIG_EFFECT_TIME, $scopeType, $scopeCode);
    }
}