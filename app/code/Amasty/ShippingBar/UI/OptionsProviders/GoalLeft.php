<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\UI\OptionsProviders;

class GoalLeft implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 100,
                'label' => __('Initial State')
            ],
            [
                'value' => 50,
                'label' => __('In Progress State')
            ],
            [
                'value' => 0,
                'label' => __('Achieved State')
            ],
        ];
    }
}
