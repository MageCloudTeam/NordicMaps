<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kss\Plugin\Provider\Base;

use Klarna\Kco\Model\Checkout\Configuration\SettingsProvider;
use Klarna\Kss\Model\KssConfigProvider;
use Magento\Store\Model\Store;

/**
 * @internal
 */
class ConfigPlugin
{
    /**
     * @var KssConfigProvider
     */
    private $configProvider;

    /**
     * @param KssConfigProvider $configProvider
     * @codeCoverageIgnore
     */
    public function __construct(KssConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * Setting the KSS flag to true
     *
     * @param SettingsProvider $config
     * @param bool $result
     * @param Store|null $store
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsShippingInIframe(
        SettingsProvider $config,
        bool $result,
        Store $store = null
    ) {
        if (!$result) {
            return $this->configProvider->isKssEnabled($store);
        }

        return $result;
    }
}
