/*
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define(['jquery', 'owl_carousel'], function ($) {
    'use strict';

    return function (config, element) {
        $(element).owlCarousel(config);
    };
});
