<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\CurrencySettings\ViewModel;

use Hryvinskyi\Base\Helper\Json;
use MageCloud\CurrencySettings\Model\Config;

/**
 * Class CurrencySettings
 */
class CurrencySettings implements CurrencySettingsInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * CurrencySettingsInterface constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getCurrencySettings(): array
    {
        return [
            'is_hide_currency_symbol' => $this->getConfig()->isHideCurrencySymbol(),
            'is_replace_zeros' => $this->getConfig()->isReplaceZeros()
        ];
    }

    /**
     * @return string
     */
    public function getSerializedCurrencySettings(): string
    {
        return Json::encode($this->getCurrencySettings());
    }
}