/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 *
 * Checkout adapter for customer data storage
 *
 * @api
 */
define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, storage) {
    'use strict';

    let cacheKey = 'checkout-data',

        /**
         * @param {Object} data
         */
        saveData = function (data) {
            storage.set(cacheKey, data);
        },

        /**
         * @return {*}
         */
        getData = function () {
            var data = storage.get(cacheKey)();

            if ($.isEmptyObject(data)) {
                data = {
                    'paymentFromData': null
                };
                saveData(data);
            }

            return data;
        };

    return {
        /**
         * Setting the selected payment data pulled from persistence storage
         *
         * @param {Object} data
         */
        setPaymentFromData: function (data) {
            let obj = getData();

            obj.paymentFromData = data;
            saveData(obj);
        },

        /**
         * Pulling the selected payment data from persistence storage
         *
         * @return {*}
         */
        getPaymentFromData: function () {
            return getData().paymentFromData;
        }
    };
});
