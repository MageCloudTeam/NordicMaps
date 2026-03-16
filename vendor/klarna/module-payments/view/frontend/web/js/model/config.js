/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';

        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
        var clientToken = ko.observable(window.checkoutConfig.payment.klarna_kp.client_token),
            message = window.checkoutConfig.payment.klarna_kp.message,
            debug = window.checkoutConfig.payment.klarna_kp.debug,
            enabled = window.checkoutConfig.payment.klarna_kp.enabled,
            b2bEnabled = window.checkoutConfig.payment.klarna_kp.b2b_enabled,
            dataSharingOnload = window.checkoutConfig.payment.klarna_kp.data_sharing_onload,
            success = window.checkoutConfig.payment.klarna_kp.success,
            hasErrors = ko.observable(false),
            availableMethods = window.checkoutConfig.payment.klarna_kp.available_methods,
            redirectUrl = window.checkoutConfig.payment.klarna_kp.redirect_url,
            reloadConfigUrl = window.checkoutConfig.payment.klarna_kp.reload_checkout_config_url,
            updateSessionUrl = window.checkoutConfig.payment.klarna_kp.update_session_url,
            getQuoteStatusUrl = window.checkoutConfig.payment.klarna_kp.get_quote_status_url,
            updateQuoteEmailUrl = window.checkoutConfig.payment.klarna_kp.update_quote_email_url,
            authorizationTokenUpdateUrl = window.checkoutConfig.payment.klarna_kp.authorization_token_update_url,
            isKecSession = window.checkoutConfig.payment.klarna_kp.is_kec_session;

        return {
            hasErrors: hasErrors,
            debug: debug,
            enabled: enabled,
            b2bEnabled: b2bEnabled,
            dataSharingOnload: dataSharingOnload,
            clientToken: clientToken,
            message: message,
            success: success,
            availableMethods: availableMethods,
            redirectUrl: redirectUrl,
            reloadConfigUrl: reloadConfigUrl,
            updateSessionUrl: updateSessionUrl,
            getQuoteStatusUrl: getQuoteStatusUrl,
            updateQuoteEmailUrl: updateQuoteEmailUrl,
            authorizationTokenUpdateUrl: authorizationTokenUpdateUrl,
            isKecSession: isKecSession
        };
    }
);
