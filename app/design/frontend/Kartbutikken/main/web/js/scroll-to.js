/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */
define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {

    $.widget('kartbutikken.scrollTo', {
        options: {
            speed: 300,
            parentOffset: null,
            offsetSelector: null,
            target: null,
        },

        /**
         * @inheritdoc
         */
        _create: function () {
            if (this.options.target === null) {
                console.trace('Target is not set');
                return;
            }

            $(this.element).on('click', $.proxy(function (e) {
                e.preventDefault();

                if($(this.options.target).length === 0) {
                    return;
                }

                let scrollTop = $(this.options.target).offset().top;

                if (this.options.parentOffset) {
                    scrollTop -= this.options.parentOffset;
                }

                if (this.options.offsetSelector) {
                    scrollTop -= $(this.options.offsetSelector).height();
                }

                $([document.documentElement, document.body]).animate({scrollTop: scrollTop}, this.options.speed);
            }, this));
        }
    });

    return $.kartbutikken.scrollTo;

});