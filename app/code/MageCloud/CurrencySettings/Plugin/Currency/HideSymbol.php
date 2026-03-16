<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\CurrencySettings\Plugin\Currency;

use MageCloud\CurrencySettings\Model\Config;
use Magento\Directory\Model\Currency;
use Magento\Framework\Currency\Data\Currency as CurrencyAlias;

/**
 * Class HideSymbol
 */
class HideSymbol
{
    /**
     * @var Config
     */
    private $config;

    /**
     * HideSymbol constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Currency $subject
     * @param $price
     * @param $precision
     * @param array $options
     * @param bool $includeContainer
     * @param bool $addBrackets
     *
     * @return array
     */
    public function beforeFormatPrecision(
        Currency $subject,
        $price,
        $precision,
        $options = [],
        $includeContainer = true,
        $addBrackets = false
    ) {
        if($this->config->isHideCurrencySymbol()) {
            $options['display'] = CurrencyAlias::NO_SYMBOL;
        }

        return [$price, $precision, $options, $includeContainer, $addBrackets];
    }

    /**
     * @param Currency $subject
     * @param $return
     *
     * @return array
     */
    public function afterFormatPrecision(
        Currency $subject,
        $return
    ) {
        if($this->config->isReplaceZeros()) {
            if (substr($return, -2) == '00' && (substr(substr($return, -3), 1) == ',' || substr(substr($return, -3), 0, 1) == '.')) {
                $return = preg_replace('/(.|,)00$/', '$1-', $return);
            } else {
                $return = str_replace(',00<', ',-<', $return);
            }
        }

        return $return;
    }
}
