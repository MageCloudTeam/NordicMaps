<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kco\Model\Checkout\Checkbox;

use Klarna\Kco\Model\Checkout\Configuration\SettingsProvider;
use Klarna\Base\Helper\KlarnaConfig;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
class DataProvider
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
     * @param SettingsProvider $config
     * @param KlarnaConfig $klarnaConfig
     * @codeCoverageIgnore
     */
    public function __construct(SettingsProvider $config, KlarnaConfig $klarnaConfig)
    {
        $this->config = $config;
        $this->klarnaConfig = $klarnaConfig;
    }

    /**
     * Get the configured additional checkboxes
     *
     * NOTE: Use a plugin on this method to override which checkboxes to be displayed and if they are checked/required.
     *
     * @param CartInterface $quote
     * @return array
     */
    public function getAdditionalCheckboxes(CartInterface $quote): array
    {
        $store = $quote->getStore();
        $checkboxes = [];
        $checkboxesConfigs = json_decode(
            $this->config->getCheckoutConfig(
                'custom_checkboxes',
                $store
            ),
            true
        );

        if (count($checkboxesConfigs) === 0) {
            return [];
        }

        foreach ($checkboxesConfigs as $checkboxesConfig) {
            $checkboxesConfig['checked'] = (bool)$checkboxesConfig['checked'];
            $checkboxesConfig['required'] = (bool)$checkboxesConfig['required'];
            $checkboxes[] = $checkboxesConfig;
        }
        return $checkboxes;
    }

    /**
     * Get the text from a merchant checkbox method
     *
     * Will call merchant checkbox methods
     *
     * @param string $code
     *
     * @return string
     */
    public function getMerchantCheckboxText(string $code): string
    {
        if ($code === '-1') {
            return '';
        }

        $methodConfig = $this->klarnaConfig->getMerchantCheckboxMethodConfig($code);
        return $methodConfig->getText();
    }
}
