/*
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define(['jquery', 'swiper'], function ($, Swiper) {
    'use strict';

    return function (config, element) {

        // $.each(config, function (key) {
        //     if (key === 'navigation') {=
        //         if (config[key]['nextEl'] && $(element).find(config[key]['nextEl']).length !== 0) {
        //             config[key]['nextEl'] = $(element).find(config[key]['nextEl']).get(0);
        //         }
        //         if (config[key]['prevEl'] && $(element).find(config[key]['prevEl']).length !== 0) {
        //             config[key]['prevEl'] = $(element).find(config[key]['prevEl']).get(0);
        //         }
        //     }
        // });

        config['on'] = {
            init: function () {
                $(document).trigger('initialized_swiper');
            },
        };

        new Swiper(element, config);
    };
});
