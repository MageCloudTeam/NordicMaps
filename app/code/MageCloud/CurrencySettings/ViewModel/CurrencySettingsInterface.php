<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\CurrencySettings\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

interface CurrencySettingsInterface extends ArgumentInterface
{
    /**
     * @return array
     */
    public function getCurrencySettings(): array;

    /**
     * @return string
     */
    public function getSerializedCurrencySettings(): string;
}