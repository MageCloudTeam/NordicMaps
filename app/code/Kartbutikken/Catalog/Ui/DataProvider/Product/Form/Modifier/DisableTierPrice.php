<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Hryvinskyi\Base\Helper\VarDumper;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;

/**
 * Class DisableTierPrice
 */
class DisableTierPrice extends AbstractModifier
{
    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {

        if(!isset($meta['advanced_pricing_modal']['children']['advanced-pricing'])) {
            return $meta;
        }


        foreach (
            $meta['advanced_pricing_modal']['children']['advanced-pricing']['children']
                 ['tier_price']['children']['record']['children'] as $key => &$value
        ) {
            $value['arguments']['data']['config']['disabled'] = true;
            $value['arguments']['data']['config']['additionalClasses'] = 'point-events-none';

            if($key === 'price_value') {
                $value['arguments']['data']['config']['children']['value_type']['arguments']['data']['config']['disabled'] = true;
                $value['arguments']['data']['config']['children']['value_type']['arguments']['data']['config']['additionalClasses'] = 'point-events-none';
            }
//            VarDumper::dump($meta['advanced_pricing_modal']['children'], 20, 1);
//            exit;
        }
        return $meta;
    }
}