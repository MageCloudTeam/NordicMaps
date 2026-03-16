/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_FastOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'mage/template',
    'mage/url',
    'jquery/ui',
    'bss/fastorder'
], function ($, mageTemplate, urlBuilder) {
    'use strict';

    $.widget('bss.integrate_ConfigurableGridView', {
        options: {
            formSelector: "#bss-fastorder-form",
            rowPrefixSelector: '#bss-fastorder-',
            configurableRowSelector: '#bss_configurablegridview tr',
            optionsSelector: '#bss-fastorder-form-option .product-custom-option',
            configurableGridTableSelector: '#bss_configurablegridview'
        },

        /**
         * @private
         */
        _create: function () {
            var self = this;

            // hide all custom options
            $('body').bind('popupIsShow', function () {
                var optionsSelector = $(self.options.configurableGridTableSelector).parent().find('.bss-options-info');
                if (optionsSelector.length > 0) {
                    optionsSelector.hide();
                }
            });


            // active module BSS Configurable Grid Table View
            $('body').bind('selectOptionClicked', function (e, triggerData) {
                self.addChildProductToFastOrderForm(triggerData);
            });
        },

        /**
         * @param triggerData
         */
        addChildProductToFastOrderForm: function (triggerData) {
            var self = this;
            var productList = [];
            var popupNode = triggerData.popupNode;
            var parentProductId = triggerData.productId;
            var sortOrder = triggerData.sortOrder;
            var rowSelector = $(self.options.rowPrefixSelector + sortOrder);
            var flagRemoveParentRow = false;
            var fastOrderWidget = $(self.options.rowPrefixSelector + '0').fastorder({});

            popupNode.find(self.options.configurableRowSelector).each(function () {
                var elQty = parseFloat($(this).find('.qty_att_product.qty').val());
                if (elQty > 0) {
                    flagRemoveParentRow = true;
                    productList.push({
                        qty: elQty,
                        sku: $(this).find('td.sku').text(),
                    })
                }
            });

            if (productList.length > 0) {
                var fetchUrl = urlBuilder.build("fastorder/index/getChildProductData");
                $.ajax({
                    url: fetchUrl,
                    data: {
                        parentProductId: parentProductId,
                        productList: productList
                    },
                    type: 'post',
                    dataType: 'json',
                    showLoader: true,
                    global: false,
                    success: function (response) {
                        var responseData = response.data;
                        if (responseData.length > 0) {
                            fastOrderWidget.fastorder('addRow', responseData.length);
                            fastOrderWidget.fastorder('handleResponse', responseData, false);

                            document.getElementById("checkProductExists").disabled = false;
                        }
                    },
                });
            }
            if (flagRemoveParentRow) {
                // remove parent product
                rowSelector.hide();
                rowSelector.find('.bss-fastorder-hidden input.bss-product-id[value="' + parentProductId + '"]').val('');
                rowSelector
                    .find('.bss-fastorder-row-qty')
                    .find('.bss-product-id-calc').val('');
            }
        }
    });

    return $.bss.integrate_ConfigurableGridView;
});
