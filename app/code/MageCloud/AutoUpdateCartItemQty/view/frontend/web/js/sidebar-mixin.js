/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function () {
        return $.widget('mage.sidebar', $.mage.sidebar, {

            _initContent: function () {
                let self = this,
                    event = {};

                self._super();

                // self._off(this.element, 'click ' + this.options.item.button);
                self._off(this.element, 'change ' + this.options.item.qty);
                self._off(this.element, 'keyup ' + this.options.item.qty);

                event['change ' + this.options.item.qty] = function (event) {
                    event.preventDefault();
                    self._updateItemQty($(event.currentTarget));
                };

                self._on(this.element, event);
            },

        });
    }
});