<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Free Shipping Bar for Magento 2
 */

namespace Amasty\ShippingBar\UI\OptionsProviders;

class GoalSource implements \Magento\Framework\Data\OptionSourceInterface
{
    public const USE_GOAL = 0;
    public const USE_FREE_SHIP_CONFIG = 1;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::USE_GOAL,
                'label' => __('Specify manually')
            ],
            [
                'value' => self::USE_FREE_SHIP_CONFIG,
                'label' => __('Free Shipping Configuration')
            ],
        ];
    }
}
