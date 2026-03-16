<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\CurrencySettings\Plugin\Format;

use MageCloud\CurrencySettings\Model\Config;
use Magento\Framework\Locale\Format;

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
     * @param Format $subject
     * @param $return
     *
     * @return mixed
     */
    public function afterGetPriceFormat(
        Format $subject,
        $return
    ) {
        if($this->config->isReplaceZeros()) {
            $return['pattern'] = '%s';
        }

        return $return;
    }
}
