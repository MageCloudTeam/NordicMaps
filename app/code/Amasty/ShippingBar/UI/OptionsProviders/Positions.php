<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\UI\OptionsProviders;

class Positions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Constants defined for bar position values
     */
    public const TOP_UNFIXED = 10;

    public const TOP_FIXED = 15;

    public const BOTTOM_UNFIXED = 20;

    public const BOTTOM_FIXED = 25;
    /**#@-*/

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TOP_UNFIXED,
                'label' => __('Page Top, static')
            ],
            [
                'value' => self::TOP_FIXED,
                'label' => __('Page Top, fixed (sticky)')
            ],
            [
                'value' => self::BOTTOM_UNFIXED,
                'label' => __('Page Bottom, static')
            ],
            [
                'value' => self::BOTTOM_FIXED,
                'label' => __('Page Bottom, fixed (sticky)')
            ]
        ];
    }
}
