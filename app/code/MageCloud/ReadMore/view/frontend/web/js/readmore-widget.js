/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define(['jquery', 'readMore'], function ($) {
    'use strict';

    return function (config) {
        $.extend(config, {
            afterExpand: function () {
                let mapIframe = $(config.element).find('iframe');

                if(mapIframe.length !== 0) {
                    mapIframe.each(function (key, value) {
                        $(value).attr('src', $(value).attr('src') ? $(value).attr('src') : $(value).attr('data-src'));
                    });
                }
            }
        });

        $(config.element).expander(config);
    };
});