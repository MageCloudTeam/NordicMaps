<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigationUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationUltimate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CmsPosition
 * @package Mageplaza\LayeredNavigationUltimate\Model\Config\Source
 */
class CmsPosition implements ArrayInterface
{
    const TOP = '1';
    const BOTTOM = '2';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('-- Please select --'),
                'value' => '',
            ],
            [
                'label' => __('Top'),
                'value' => self::TOP,
            ],
            [
                'label' => __('Bottom'),
                'value' => self::BOTTOM,
            ],
        ];
    }
}
