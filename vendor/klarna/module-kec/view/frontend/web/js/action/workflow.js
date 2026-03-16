/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
    [
        'mage/storage',
        'Magento_Customer/js/customer-data',
        'jquery',
        'mage/url',
        'mage/translate'
    ],
    function (storage,
        customerData,
        $,
        urlBuilder,
        $t
    ) {
        'use strict';

        return function execute(klarnaResult, parameter)
        {
            if (!klarnaResult.approved) {
                return;
            }

            $('body').trigger('processStart');
            if (klarnaResult.approved) {
                parameter.set('client_token', klarnaResult.client_token);
                parameter.set(
                    'addresses',
                    JSON.stringify({'shipping_address': klarnaResult.collected_shipping_address})
                );
                $.ajax({
                    url: urlBuilder.build('kec/klarna/updateQuoteAddress'),
                    data: parameter,
                    type: 'post',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (data) {
                        $('body').trigger('processStop');

                        if (data.status !== 200) {
                            customerData.set('messages', {
                                messages: [{
                                    type: 'error',
                                    text: $t('Error when updating the address of the customer.')
                                }]
                            });
                        } else {
                            customerData.reload(['cart'], true);
                            storage = JSON.parse(localStorage.getItem('mage-cache-storage'));
                            if (!storage.hasOwnProperty('checkout-data')) {
                                storage['checkout-data'] = {};
                            }
                            storage['checkout-data'].selectedPaymentMethod = data.method;
                            localStorage.setItem('mage-cache-storage', JSON.stringify(storage));
                            window.location.href = data.url;
                        }
                    }
                });
            }
        };
    }
);
