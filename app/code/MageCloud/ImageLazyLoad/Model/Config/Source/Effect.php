<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\ImageLazyLoad\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Effect
 */
class Effect implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'show', 'label' => __('Show')], ['value' => 'fadeIn', 'label' => __('FadeIn')]];
    }
}