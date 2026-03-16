<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Providing configuration values
 *
 * @internal
 */
class KssConfigProvider
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @codeCoverageIgnore
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns true if KSS is enabled in the admin
     *
     * @param StoreInterface $store
     * @return bool
     */
    public function isKssEnabled(StoreInterface $store): bool
    {
        return $this->scopeConfig->isSetFlag(
            'payment/klarna_kss/enabled',
            ScopeInterface::SCOPE_STORES,
            $store
        );
    }
}
