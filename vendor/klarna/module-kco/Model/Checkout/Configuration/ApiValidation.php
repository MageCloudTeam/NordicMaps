<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Checkout\Configuration;

use Klarna\Base\Exception as KlarnaException;
use Klarna\Base\Helper\KlarnaConfig;
use Klarna\Kco\Model\Checkout\Configuration\SettingsProvider;
use Klarna\Kco\Model\Configuration\Checkout;
use Magento\Store\Api\Data\StoreInterface;

/**
 * @internal
 */
class ApiValidation
{
    /**
     * @var SettingsProvider
     */
    private SettingsProvider $config;
    /**
     * @var KlarnaConfig
     */
    private KlarnaConfig $klarnaConfig;
    /**
     * @var Checkout
     */
    private Checkout $checkoutConfiguration;

    /**
     * @param SettingsProvider $config
     * @param KlarnaConfig $klarnaConfig
     * @param Checkout $checkoutConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(SettingsProvider $config, KlarnaConfig $klarnaConfig, Checkout $checkoutConfiguration)
    {
        $this->config = $config;
        $this->klarnaConfig = $klarnaConfig;
        $this->checkoutConfiguration = $checkoutConfiguration;
    }

    /**
     * Checking if an KP api endpoint is used
     *
     * @param StoreInterface $store
     * @throws KlarnaException
     */
    public function checkKpApiEndpointUsed(StoreInterface $store): void
    {
        $api = $this->klarnaConfig->getVersionConfig($store)->getCode();
        if (!$api) {
            return;
        }

        if (substr($api, 0, 3) === 'kp_') {
            throw new KlarnaException(__(
                'A Klarna Payments endpoint is currently selected. ' .
                'Please select a Klarna Checkout endpoint in your admin configuration.'
            ));
        }
    }

    /**
     * Checking if KP and KCO are both enabled
     *
     * @param StoreInterface $store
     *
     * @throws KlarnaException
     */
    public function checkKpKcoEnabled(StoreInterface $store): void
    {
        $kpFlag = $this->checkoutConfiguration->isKpEnabled($store);
        $kcoFlag = $this->config->isKlarnaCheckoutPaymentEnabled($store);

        if ($kpFlag && $kcoFlag) {
            throw new KlarnaException(__(
                'Klarna Checkout can not be rendered since Klarna Payments and Klarna Checkout are both enabled. ' .
                'Please disable Klarna Payments in your admin configuration when using Klarna Checkout.'
            ));
        }
    }
}
