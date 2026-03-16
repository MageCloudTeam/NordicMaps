<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\WeltPixelOwlCarouselSlider\Model\Swiper;

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

        $navigation = [
            'nextEl' => '.swiper-button-next',
            'prevEl' => '.swiper-button-prev',
        ];
        $pagination = [
            'el' => '.swiper-pagination',
            'type' => 'bullets',
        ];

        foreach ($config as $key => &$item) {
            if ($key == 'stagePadding' || $key == 'URLhashListener' || $key == 'show_price' || $key == 'show_addto' || $key == 'show_wishlist' || $key == 'show_compare' || $key == 'period' || $key == 'title' || $key == 'status' || $key == 'center') {
                unset($item);
                continue;
            }

            if ($key == 'slide_by') {
                $return['initialSlide'] = (int)$item - 1;
                unset($item);
                continue;
            }

            if ($key == 'margin') {
                $return['spaceBetween'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'nav_brk1') {
                $return['navigation'] = !!$item ? $navigation : false;
                unset($item);
                continue;
            }

            if ($key == 'dots_brk1') {
                $return['pagination'] = !!$item ? $pagination : false;
                unset($item);
                continue;
            }

            if ($key == 'items_brk1') {
                $return['slidesPerView'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'center_brk1') {
                $return['centeredSlides'] = !!$item;
                unset($item);
                continue;
            }

            if ($key == 'stagePadding_brk1') {
//                $return['breakpoints'][$breakPoints['breakpoint_1']]['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'nav_brk2') {
                $return['breakpoints'][$breakPoints['breakpoint_2']]['navigation'] = !!$item ? $navigation : false;
                unset($item);
                continue;
            }

            if ($key == 'dots_brk2') {
                $return['breakpoints'][$breakPoints['breakpoint_2']]['pagination'] = !!$item ? $pagination : false;
                unset($item);
                continue;
            }

            if ($key == 'items_brk2') {
                $return['breakpoints'][$breakPoints['breakpoint_2']]['slidesPerView'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'center_brk2') {
                $return['breakpoints'][$breakPoints['breakpoint_2']]['centeredSlides'] = !!$item;
                unset($item);
                continue;
            }

            if ($key == 'stagePadding_brk2') {
//                $return['breakpoints'][$breakPoints['breakpoint_2']]['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'nav_brk3') {
                $return['breakpoints'][$breakPoints['breakpoint_3']]['navigation'] = !!$item ? $navigation : false;
                unset($item);
                continue;
            }

            if ($key == 'dots_brk3') {
                $return['breakpoints'][$breakPoints['breakpoint_3']]['pagination'] = !!$item ? $pagination : false;
                unset($item);
                continue;
            }

            if ($key == 'items_brk3') {
                $return['breakpoints'][$breakPoints['breakpoint_3']]['slidesPerView'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'center_brk3') {
                $return['breakpoints'][$breakPoints['breakpoint_3']]['centeredSlides'] = !!$item;
                unset($item);
                continue;
            }

            if ($key == 'stagePadding_brk3') {
//                $return['breakpoints'][$breakPoints['breakpoint_3']]['stagePadding'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'nav_brk4') {
                $return['breakpoints'][$breakPoints['breakpoint_4']]['navigation'] = !!$item ? $navigation : false;
                unset($item);
                continue;
            }

            if ($key == 'dots_brk4') {
                $return['breakpoints'][$breakPoints['breakpoint_4']]['pagination'] = !!$item ? $pagination : false;
                unset($item);
                continue;
            }

            if ($key == 'items_brk4') {
                $return['breakpoints'][$breakPoints['breakpoint_4']]['slidesPerView'] = (int)$item;
                unset($item);
                continue;
            }

            if ($key == 'center_brk4') {
                $return['breakpoints'][$breakPoints['breakpoint_4']]['centeredSlides'] = !!$item;
                unset($item);
                continue;
            }

            if ($key == 'stagePadding_brk4') {
//                $return['breakpoints'][$breakPoints['breakpoint_4']]['stagePadding'] = (int)$item;
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
