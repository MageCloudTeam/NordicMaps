<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\WeltPixelOwlCarouselSlider\Model\Owl;

/**
 * Class ConfigurationConverter
 */
class ConfigurationConverter
{
    /**
     * @param array $config
     * @param array $breakPoints
     *
     * @return array
     */
    public function convert(array $config, array $breakPoints): array
    {
        $return = [];

        foreach ($config as $key => &$item) {
            if ($key == 'URLhashListener' || $key == 'show_price' || $key == 'show_addto' || $key == 'show_wishlist' || $key == 'show_compare' || $key == 'period' || $key == 'title' || $key == 'status') {
                unset($item);
                continue;
            }

            if($key == 'slide_by') {
                $return['slideBy'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'stagePadding') {
                $return['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'margin') {
                $return['margin'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'nav_brk1') {
                $return['responsive'][$breakPoints['breakpoint_1']]['nav'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'dots_brk1') {
                $return['responsive'][$breakPoints['breakpoint_1']]['dots'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'items_brk1') {
                $return['responsive'][$breakPoints['breakpoint_1']]['items'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'center_brk1') {
                $return['responsive'][$breakPoints['breakpoint_1']]['center'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'stagePadding_brk1') {
                $return['responsive'][$breakPoints['breakpoint_1']]['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'nav_brk2') {
                $return['responsive'][$breakPoints['breakpoint_2']]['nav'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'dots_brk2') {
                $return['responsive'][$breakPoints['breakpoint_2']]['dots'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'items_brk2') {
                $return['responsive'][$breakPoints['breakpoint_2']]['items'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'center_brk2') {
                $return['responsive'][$breakPoints['breakpoint_2']]['center'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'stagePadding_brk2') {
                $return['responsive'][$breakPoints['breakpoint_2']]['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'nav_brk3') {
                $return['responsive'][$breakPoints['breakpoint_3']]['nav'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'dots_brk3') {
                $return['responsive'][$breakPoints['breakpoint_3']]['dots'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'items_brk3') {
                $return['responsive'][$breakPoints['breakpoint_3']]['items'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'center_brk3') {
                $return['responsive'][$breakPoints['breakpoint_3']]['center'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'stagePadding_brk3') {
                $return['responsive'][$breakPoints['breakpoint_3']]['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'nav_brk4') {
                $return['responsive'][$breakPoints['breakpoint_4']]['nav'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'dots_brk4') {
                $return['responsive'][$breakPoints['breakpoint_4']]['dots'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'items_brk4') {
                $return['responsive'][$breakPoints['breakpoint_4']]['items'] = (int)$item;
                unset($item);
                continue;
            }

            if($key == 'center_brk4') {
                $return['responsive'][$breakPoints['breakpoint_4']]['center'] = !!$item;
                unset($item);
                continue;
            }

            if($key == 'stagePadding_brk4') {
                $return['responsive'][$breakPoints['breakpoint_4']]['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if ($item === '0' || $item === '1') {
                $return[$key] = !!$item;
            }
        }
        
        return $return;
    }
}