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
 * @category  BSS
 * @package   Bss_FastOrder
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
define(
    [
        'jquery',
        'bss/fastorder_option'
    ], function ($) {
        'use strict';

        return function (config) {
            var template = $('#baseUrlLoading').attr('data-template');
            $(document).on("mousedown", "#bss-fastorder-form .bss-row-suggest",function (e) {
                var widget = $(this).fastorder_option({});
                if ($(this).find('.bss-show-popup').val() == 1) {
                    var dataPopups = JSON.parse(JSON.stringify(localStorage.getItem('dataPopups')));
                    if (dataPopups == null) {
                        dataPopups = [];
                    } else {
                        dataPopups = JSON.parse(dataPopups);
                    }

                    var dataPopup = {
                        sortOrder : $(this).closest('.bss-fastorder-row.bss-row').attr('data-sort-order'),
                        productId : $(this)
                            .closest('.bss-fastorder-row.bss-row')
                            .find('.bss-fastorder-row-ref .bss-product-id')
                            .val()
                    };
                    dataPopups.push(dataPopup);
                    localStorage.setItem('dataPopups', JSON.stringify(dataPopups));

                    if (template == 'template-1') {
                        widget.fastorder_option('showPopup', config.selectUrl, this);
                    }
                }
                widget.fastorder_option('selectProduct', this);
            });
        }
    });
