/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */

define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data'
], function ($, _, customerData) {
    'use strict';

    $.widget('magecloud.dataLayer', {
        options: {
            namespace: "magecloud-datalayer",
            allowPushToDataLayer: true,
            dataLayerContainer: "[data-role=magecloud-data-layer-container]"
        },

        /**
         * DataLayer creation
         * @protected
         */
        _create: function () {
            $(document).on('ajaxComplete', $.proxy(this._applyEvents, this));
        },

        /**
         * Apply new dataLayer event
         * @protected
         * @param {Object} event - object
         * @param {Object} jqXHR - The jQuery XMLHttpRequest object returned by $.ajax()
         * @param settings
         */
        _applyEvents: function (event, jqXHR, settings) {
            if (settings.url.search('/customer\/section\/load/') > 0) {
                let response = jqXHR.responseJSON,
                    self = this;

                if (response.hasOwnProperty(this.options.namespace)) {
                    _.each(response[this.options.namespace].events, function (eventData) {
                        $(self.options.dataLayerContainer).html(eventData);
                    });
                }
            }
        },

        /**
         * Removes dataLayer
         */
        _clearEvents: function () {
            $(this.options.dataLayerContainer).html('');
        }
    });

    return $.magecloud.dataLayer;
});
