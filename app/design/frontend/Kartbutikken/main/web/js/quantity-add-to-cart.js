/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {

    $.widget('mage.quantityAddToCart', {
        options: {
            plusText: '+',
            minusText: '-',
            defaultValue: '1',
            minus: null,
            minusButton: null,
            plus: null,
            plusButton: null,
            control: null,
            input: null,
            inputWrapper: null,
        },
        oldValue: 0,
        minus: null,
        minusButton: null,
        plus: null,
        plusButton: null,
        control: null,
        input: null,
        inputWrapper: null,

        /**
         * @inheritdoc
         */
        _create: function () {
            this._render();
            this._bind();
        },

        /**
         * Render element
         *
         * @private
         */
        _render: function () {
            let element = $(this.element);

            this.minus = element.find('.qty-minus');
            this.minusButton = element.find('.btn-qty.minus');
            this.plus = element.find('.qty-plus');
            this.plusButton = element.find('.btn-qty.plus');
            this.control = element.find('.control');
            this.input = element.find('.input-text.qty');
            this.inputWrapper = element.find('.qty-input');
        },

        /**
         * Bind events
         *
         * @private
         */
        _bind: function () {
            this.element.parent().on('click', '.btn-qty', $.proxy(function (event) {
                let newVal,
                    button = $(event.target),
                    oldValue = this.input.val();

                if (button.hasClass('plus')) {
                    newVal = parseFloat(oldValue) + 1;
                }

                if (button.hasClass('minus')) {
                    newVal = oldValue > this.options.defaultValue ?
                        parseFloat(oldValue) - 1 : this.options.defaultValue;
                }

                this.input.val(newVal);
                event.preventDefault();
            }, this));
        }
    });

    return $.mage.quantityAddToCart;
});

