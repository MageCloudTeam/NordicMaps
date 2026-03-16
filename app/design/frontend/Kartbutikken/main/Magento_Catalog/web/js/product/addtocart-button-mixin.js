/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define([
    'jquery',
    'mage/translate',
    'ko'
], function ($, $t, ko) {
    'use strict';

    return function (extend) {
        return extend.extend({

            formKeyInput: ko.observable(''),

            initialize: function () {
                this._super();

                this.createFormKeyInput('form_key', this.getFormKey());

                return this;
            },

            /**
             * Create hidden input with formKey
             *
             * @param name
             * @param value
             */
            createFormKeyInput: function (name, value) {
                let input = '<input name="' + name + '" value="' + value + '" type="hidden">';
                this.formKeyInput(input);
            },

            /**
             * Get formKey
             *
             * @returns {string}
             */
            getFormKey: function () {
                return $.cookie('form_key');
            },

            getUnec: function (row) {
                return JSON.parse(this.getDataPost(row)).data.uenc;
            },

            getLabel: function () {
                return $t('Add to cart');
            }
        });
    };
});
