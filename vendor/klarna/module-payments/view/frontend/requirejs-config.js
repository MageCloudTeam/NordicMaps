/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/get-payment-information': {
                'Klarna_Kp/js/action/get-payment-information-override': true
            },
            'Magento_Checkout/js/action/select-shipping-method': {
                'Klarna_Kp/js/action/reload-payment-configuration': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'Klarna_Kp/js/action/reload-payment-configuration': true
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'Klarna_Kp/js/action/reload-payment-configuration': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Klarna_Kp/js/action/reload-payment-configuration': true
            }
        }
    },
    paths: {
        klarnapi: 'https://x.klarnacdn.net/kp/lib/v1/api'
    }
};
