<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\HideDefaultStoreCode\Model;

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
    const XML_PATH_HIDE_DEFAULT_STORE_CODE = 'web/url/hide_default_store_code';

    /**
     * Scope configuration
     *
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
     * Is enable hide default store code in url
     *
     * @param string|null $scopeCode
     *
     * @return boolean
     */
    public function isHideDefaultStoreCode(string $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HIDE_DEFAULT_STORE_CODE,
            ScopeInterface::SCOPE_WEBSITE,
            $scopeCode
        );
    }
}