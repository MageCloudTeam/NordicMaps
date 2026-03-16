<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\Model\Config\Catalog;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Frontend
 */
class Frontend
{
    /**
     * Configuration paths
     */
    const XML_CONFIG_SHOW_QUANTITY = 'catalog/frontend/show_quantity';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Header constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isShowQuantity(?string $scopeCode = null, string $scopeType = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_SHOW_QUANTITY, $scopeType, $scopeCode);
    }
}