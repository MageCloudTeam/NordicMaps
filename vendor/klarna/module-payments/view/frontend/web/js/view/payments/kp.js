/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
    [
        'ko',
        'jquery',
        'mage/translate',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/view/billing-address',
        'Klarna_Kp/js/model/config',
        'Klarna_Kp/js/model/klarna',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Klarna_Kp/js/view/payments',
        'Klarna_Kp/js/model/debug',
        'Klarna_Kp/js/action/set-payment-method-action',
        'Magento_Checkout/js/action/redirect-on-success',
        'mage/storage',
        'Magento_Customer/js/customer-data'
    ],
    function (ko,
        $,
        $t,
        Component,
        billingAddress,
        config,
        klarna,
        quote,
        customer,
        additionalValidators,
        kp,
        debug,
        setPaymentMethodAction,
        redirectOnSuccessAction,
        storage,
        customerData) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Klarna_Kp/payments/kp',
                timeoutMessage: 'Sorry, but something went wrong. Please contact the seller.',
                redirectAfterPlaceOrder: false
            },
            placeOrderHandler: null,
            validateHandler: null,

            isVisible: ko.observable(true),
            isLoading: false,
            isBillingSameAsShipping: true,
            showButton: ko.observable(false),
            orderButton: $('.action.primary').filter('[class*="checkout"]'),

            /**
       * Checking if the payment is preselected
       */
            checkPreSelect: function () {
                if (this.getCode() === this.isChecked()) {
                    this.isLoading = false;
                    this.debounceKlarnaLoad();
                }
            },

            /**
       * Getting back the logo url
       * @returns {String}
       */
            getLogoUrl: function () {
                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                if (window.checkoutConfig.payment.klarna_kp[this.getCode()]) {

                    return window.checkoutConfig.payment.klarna_kp[this.getCode()].logo;
                }

                return '';
            },

            /**
       * Getting back the logo id
       * @returns {String}
       */
            getLogoId: function () {
                return 'klarna_logo_id_' + this.getCode();
            },

            /**
       * Setting the place order handler
       * @param {Object} handler
       */
            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },

            /**
       * Setting the validation handler
       * @param {Object} handler
       */
            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },

            /**
       * Returning the object
       * @returns {Object}
       */
            context: function () {
                return this;
            },

            /**
       * Return the flag for showing the legend
       * @returns {Boolean}
       */
            isShowLegend: function () {
                return true;
            },

            /**
       * Getting back the title
       * @returns {String}
       */
            getTitle: function () {
                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                if (window.checkoutConfig.payment.klarna_kp[this.getCode()]) {

                    return window.checkoutConfig.payment.klarna_kp[this.getCode()].title;
                }

                return 'Klarna Payments';
            },

            /**
       * Get data
       * @returns {Object}
       */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'method_title': this.getTitle(),
                        'logo': this.getLogoUrl()
                    }
                };
            },

            /**
       * Getting back the category id
       * @returns {String}
       */
            getCategoryId: function () {
                // Strip off "klarna_"
                return this.getCode().substr(7);
            },

            /**
       * Returning the result if there is a message
       * @returns {Boolean}
       */
            hasMessage: function () {
                return config.message !== null || config.clientToken() === null || config.clientToken() === '';
            },

            /**
       * Getting back the message
       * @returns {String}
       */
            getMessage: function () {
                if (config.message !== null) {
                    return config.message;
                }

                return $t('An unknown error occurred. Please try another payment method');
            },

            /**
       * disable the place order button
       */
            disableElement: function (element) {
                $(element)
                    .prop('disabled', true)
                    .addClass('disabled');
            },

            /**
       * enable the place order button
       */
            enableElement: function (element) {
                $(element)
                    .prop('disabled', false)
                    .removeClass('disabled');
            },

            /**
       * Getting back the client token
       * @returns {String}
       */
            getClientToken: function () {
                return config.clientToken();
            },

            /**
       * Performing the initialize workflow
       */
            initialize: function () {
                var self = this;

                this._super();
                this.showButton(false);

                if (this.hasMessage()) {
                    // Don't try to initialize Klarna
                    return;
                }
                klarna.init();
                quote.paymentMethod.subscribe(function (value) {
                    self.isLoading = false;

                    if (value && value.method === self.getCode()) {
                        self.debounceKlarnaLoad();
                    }
                });
                config.hasErrors.subscribe(function (value) {
                    self.showButton(value);
                });

                billingAddress().isAddressSameAsShipping.subscribe(function (isSame) {
                    self.isBillingSameAsShipping = isSame;
                });
                quote.shippingAddress.subscribe(function () {
                    // MAGE-803: When billing and shipping are the same, both the shipping and billing listeners will be
                    // called with the shipping one called first. If we allow this to update KP in that case then the
                    // billing address will not match between Magento and Klarna as by the time it reaches here the
                    // address change will not have propagated to the billing address in the Magento quote and the
                    // billing listener will be blocked from updating KP as an update will already be in progress.
                    if (self.getCode() === self.isChecked() && !self.isBillingSameAsShipping) {
                        self.debounceKlarnaLoad();
                    }
                });
                quote.billingAddress.subscribe(function () {
                    if (self.getCode() === self.isChecked()) {
                        self.debounceKlarnaLoad();
                    }
                });

                // eslint-disable-next-line no-unused-vars
                quote.totals.subscribe(function (newTotals) {
                    if (self.getCode() === self.isChecked()) {
                        self.debounceKlarnaLoad();
                    }
                });
            },

            /**
       * Getting back the container id
       * @returns {String}
       */
            getContainerId: function () {
                return this.getCode().replace(new RegExp('_', 'g'), '-') + '-container';
            },

            /**
       * Selecting the payment method
       * @returns {*}
       */
            selectPaymentMethod: function () {
                this.isLoading = false;
                this.debounceKlarnaLoad();

                return this._super();
            },
            loadTimeout: null,

            /**
       * Debouncing the Klarna load
       */
            debounceKlarnaLoad: function () {
                var self = this;

                if (self.loadTimeout) {
                    clearTimeout(self.loadTimeout);
                }
                self.loadTimeout = setTimeout(function () {
                    self.loadKlarna();
                }, 200);
            },

            /**
       * Loading Klarna
       * @returns {Boolean}
       */
            loadKlarna: function () {
                var self = this;

                if (self.isLoading) {
                    return false;
                }
                self.isLoading = true;

                try {
                    klarna.load(self.getCategoryId(), self.getContainerId(), function (res) {
                        debug.log(res);
                        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                        self.showButton(res.show_form);
                        self.isLoading = false;
                    });

                    return true;
                } catch (e) {
                    debug.log(e);
                    self.isLoading = false;

                    return false;
                }
            },

            /**
       * Sending the Klarna authorize request
       */
            authorize: function () {
                var self = this;

                if (additionalValidators.validate()) {
                    self.showButton(false);
                    self.disableElement(self.orderButton);

                    if (this.hasMessage()) {

                        return;
                    }

                    if (customer.isLoggedIn()) {
                        this.performAuthorizationWorkflow(self.getCategoryId(), self);
                    } else {
                        this.updateEmailAndPerformAuthorizationWorkflow(self.getCategoryId(), self);
                    }
                }
            },

            /**
       * Updating the email because for guests it can be zero when the billing and shipping address are different.
       * After it performing the authorization workflow.
       *
       * @param categoryId
       * @param context
       */
            updateEmailAndPerformAuthorizationWorkflow: function (categoryId, context) {
                storage.post(
                    config.updateQuoteEmailUrl,
                    JSON.stringify({'email': quote.guestEmail}),
                    false,
                    'application/json'
                ).done(function () {
                    context.performAuthorizationWorkflow(categoryId, context);
                });
            },

            /**
       * Performing the full authorization workflow
       */
            performAuthorizationWorkflow: function (categoryId, context) {
                klarna.authorize(categoryId, klarna.getUpdateData(), function (klarnaAuthorizeResult) {
                    debug.log(klarnaAuthorizeResult);
                    if (!klarnaAuthorizeResult.approved) {
                        return;
                    }

                    $('body').trigger('processStart');

                    storage.post(
                        config.getQuoteStatusUrl,
                        JSON.stringify({'authorization_token': klarnaAuthorizeResult.authorization_token}),
                        false,
                        'application/json'
                    ).done(function (result) {
                        if (result['is_active'] === '1') {
                            if (klarnaAuthorizeResult.approved) {
                                storage.put(
                                    config.authorizationTokenUpdateUrl,
                                    JSON.stringify({'authorization_token': klarnaAuthorizeResult.authorization_token}),
                                    false,
                                    'application/json'
                                ).done(function () {
                                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                                    context.placeOrder();
                                });
                            }
                            return;
                        }

                        if (klarnaAuthorizeResult.approved) {
                            storage.post(
                                config.updateSessionUrl,
                                JSON.stringify({'authorization_token': klarnaAuthorizeResult.authorization_token}),
                                false,
                                'application/json'
                            ).done(function () {
                                var clearData = {
                                        'selectedShippingAddress': null,
                                        'shippingAddressFromData': null,
                                        'newCustomerShippingAddress': null,
                                        'selectedShippingRate': null,
                                        'selectedPaymentMethod': null,
                                        'selectedBillingAddress': null,
                                        'billingAddressFromData': null,
                                        'newCustomerBillingAddress': null
                                    },
                                    sections = ['cart'];

                                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                                context.showButton(klarnaAuthorizeResult.show_form);

                                customerData.set('checkout-data', clearData);
                                customerData.invalidate(sections);
                                customerData.reload(sections, true);

                                context.afterPlaceOrder();
                                redirectOnSuccessAction.execute();
                            });
                        }

                        $('body').trigger('processStop');
                        context.enableElement(context.orderButton);
                        context.showButton(klarnaAuthorizeResult.show_form);
                    });
                });
            },

            /**
       * Doing actions after the order was placed
       */
            afterPlaceOrder: function () {
                setPaymentMethodAction();
            }
        });
    }
);
