/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

/* @api */
define([
    'jquery',
    'uiComponent',
    'Kartbutikken_NordecakonsumentOrderAttributes/js/checkout-payment-data',
    'Kartbutikken_NordecakonsumentOrderAttributes/js/model/payment-data',
], function (
    $,
    Component,
    paymentData,
    paymentFormData,
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Kartbutikken_NordecakonsumentOrderAttributes/payment/info'
        },
        paymentFormData: paymentFormData,

        initialize: function () {
            this._super();
        },

        userChanges: function () {
            paymentData.setPaymentFromData(paymentFormData.getData());
        }
    });
});
