<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\SeoUrl\Plugin\Helper;

/**
 * Class Data
 */
class Data
{
    /**
     * @param \Mageplaza\SeoUrl\Helper\Data $subject
     * @param array $option
     *
     * @return array
     */
    public function beforeProcessKey(
        \Mageplaza\SeoUrl\Helper\Data $subject,
        \Magento\Eav\Model\Entity\Attribute\Option $option
    ) {
        $option->setData('default_value', $option->getData('store_default_value'));

        return [$option];
    }
}
