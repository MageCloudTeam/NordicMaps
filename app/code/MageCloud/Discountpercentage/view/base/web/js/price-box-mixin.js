/*
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define(['jquery', 'Magento_Catalog/js/price-utils'], function ($, utils) {
    'use strict';

    return function (priceBox) {
        $.widget('mage.priceBox', priceBox, {

            /**
             * Widget creating.
             */
            _create: function createPriceBox() {
                let self = this,
                    saveElm = self.element.find('.save-price');

                self._super();

                if (saveElm.length === 0) {
                    return;
                }

                self.element.on('updatePrice', function (event, prices) {
                    if(
                        prices &&
                        prices.prices &&
                        prices.prices.oldPrice &&
                        prices.prices.finalPrice
                    ) {
                        let finalPriceElm = self.element.find('[data-price-type="finalPrice"]'),
                            savePrice = prices.prices.oldPrice.amount - prices.prices.finalPrice.amount,
                            finalPrice = parseFloat(finalPriceElm.attr('data-price-amount')),
                            percent = 100 - (
                                ((finalPrice + prices.prices.finalPrice.amount)
                                    / (finalPrice + prices.prices.oldPrice.amount))
                                * 100
                            );

                        if (percent === 0) {
                            percent = 100 - (
                                (finalPrice / (finalPrice + parseFloat(saveElm.attr('data-price-amount')))) * 100
                            );
                        }

                        savePrice = savePrice + parseFloat(saveElm.attr('data-price-amount'));

                        saveElm.html(
                            $.mage.__('<span>Your discount is %1 (%2)</span>')
                                .replace('%1', utils.formatPrice(savePrice, self.options.priceConfig.priceFormat))
                                .replace('%2', percent.toFixed(0) + '%')
                        );
                        if (saveElm.length > 0) {

                        }
                    }
                });
            },
        });

        return $.mage.priceBox;
    };
});