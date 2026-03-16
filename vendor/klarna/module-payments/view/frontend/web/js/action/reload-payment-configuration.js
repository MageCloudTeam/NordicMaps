/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
/* eslint-disable */
define([
  'mage/utils/wrapper',
  'mage/storage',
  'Magento_Checkout/js/model/payment/renderer-list',
  'Magento_Checkout/js/model/quote',
  'Klarna_Kp/js/model/config',
  'jquery'
], function (wrapper, storage, renderer, quote, klarnaConfig, $) {
  'use strict';

  var last_shipping_country_id = '';
  var last_billing_country_id = '';
  var last_shipping_company = '';
  var last_billing_company = '';

  return function (overriddenFunction) {
    return wrapper.wrap(overriddenFunction, function (originalAction) {
      var originalResult = originalAction();

      if (typeof originalResult === 'object') {
        if (quote.billingAddress() !== null) {
          originalResult['countryId'] = quote.billingAddress().countryId;
          originalResult['company'] = quote.billingAddress().company;
        }
      }

      var recalculate_config = false;
      var ajax_params = {};

      if (quote.isVirtual() && originalResult !== undefined && 'countryId' in originalResult) {
        ajax_params = {
          billing_country_id: originalResult['countryId'],
          billing_company: originalResult['company']
        };

        if (last_billing_country_id !== ajax_params['billing_country_id']) {
          recalculate_config = true;
        }
        if (last_billing_company !== ajax_params['billing_company']) {
          recalculate_config = true;
        }

        last_billing_company = ajax_params['billing_company'];
        last_billing_country_id = ajax_params['billing_country_id'];
      } else {
        if (quote.shippingAddress() === null) {
          return originalResult;
        }

        ajax_params = {
          shipping_country_id: quote.shippingAddress() ? quote.shippingAddress().countryId : '',
          shipping_company: quote.shippingAddress() ? quote.shippingAddress().company : '',
          billing_country_id: quote.billingAddress() ? quote.billingAddress().countryId : '',
          billing_company: quote.billingAddress() ? quote.billingAddress().company : '',
        };

        if (last_shipping_country_id !== ajax_params['shipping_country_id']) {
          recalculate_config = true;
        }
        if (last_shipping_company !== ajax_params['shipping_company']) {
          recalculate_config = true;
        }
        if (last_billing_country_id !== ajax_params['billing_country_id']) {
          recalculate_config = true;
        }
        if (last_billing_company !== ajax_params['billing_company']) {
          recalculate_config = true;
        }

        last_shipping_company = ajax_params['shipping_company'];
        last_shipping_country_id = ajax_params['shipping_country_id'];
        last_billing_company = ajax_params['billing_company'];
        last_billing_country_id = ajax_params['billing_country_id'];
      }

      if (recalculate_config) {
        storage.post(klarnaConfig.reloadConfigUrl, JSON.stringify(ajax_params), false, 'application/json')
          .done(function (result) {
            var removeEntries = [];

            last_billing_company = result.billingAddressFromData ? result.billingAddressFromData.company : '';
            last_billing_country_id = result.billingAddressFromData ? result.billingAddressFromData.country_id : '';

            // Removing Klarna Payment Methods
            renderer.each(function (value, index) {
              if (value.type.startsWith('klarna_')) {
                removeEntries.push(value);
              }
            });

            $.each(removeEntries, function (index, entry) {
              renderer.remove(entry);
            })

            // Adding new Klarna Payment Methods
            $.each(result['payment'], function (index, entry) {
              if (index.startsWith('klarna_')) {
                window.checkoutConfig.payment.klarna_kp[index] = entry;
                klarnaConfig.clientToken(entry.client_token);

                renderer.push({
                  type: index,
                  component: 'Klarna_Kp/js/view/payments/kp'
                });

                // For B2B sessions a different banner is shown
                $('#klarna_logo_id_' + index).attr('src', entry.logo);
              }
            });
          });
      }

      return originalResult;
    });
  };
});
