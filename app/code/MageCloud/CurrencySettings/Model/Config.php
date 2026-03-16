<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\CurrencySettings\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
    /**
     * Configuration paths
     */
    const XML_CONF_CURRENCY_SYMBOL_HIDE_SYMBOL = 'currency/symbol/hide_symbol';
    const XML_CONF_CURRENCY_SYMBOL_REPLACE_ZERO = 'currency/symbol/replace_zero';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
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
     * @return bool
     */
    public function isHideCurrencySymbol(
        string $scopeCode = null,
        string $scopeType = ScopeInterface::SCOPE_WEBSITE
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONF_CURRENCY_SYMBOL_HIDE_SYMBOL,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return bool
     */
    public function isReplaceZeros(
        string $scopeCode = null,
        string $scopeType = ScopeInterface::SCOPE_WEBSITE
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONF_CURRENCY_SYMBOL_REPLACE_ZERO,
            $scopeType,
            $scopeCode
        );
    }
}