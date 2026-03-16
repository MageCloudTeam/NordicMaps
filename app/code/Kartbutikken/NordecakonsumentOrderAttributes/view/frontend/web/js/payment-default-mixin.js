/*
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define([
    'jquery',
    'Magento_Checkout/js/action/place-order',
    'Kartbutikken_NordecakonsumentOrderAttributes/js/model/payment-data',
], function (
    $,
    placeOrderAction,
    paymentFormData
) {
    'use strict';

    return function (target) {
        return target.extend({

            /**
             * @return {*}
             */
            getPlaceOrderDeferredObject: function () {
                return $.when(
                    placeOrderAction($.extend({}, this.getData(), {'additional_data': paymentFormData.getData()}), this.messageContainer)
                );
            },
        });
    };
});