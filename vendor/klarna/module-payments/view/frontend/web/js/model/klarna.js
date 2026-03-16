/* global Klarna */
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Klarna_Kp/js/model/config',
        'Klarna_Kp/js/model/debug',
        'Magento_Checkout/js/view/billing-address',
        'Magento_Checkout/js/action/create-billing-address',
        'klarnapi'
    ],
    function ($, quote, customer, config, debug, viewBillingAddress, createBillingAddress) {
        'use strict';

        var isBillingSameAsShipping;

        viewBillingAddress().isAddressSameAsShipping.subscribe(function (isSame) {
            isBillingSameAsShipping = isSame;
        });

        return {
            b2bEnabled: config.b2bEnabled,

            /**
             * Getting back the address based on the input
             * @param {Array} address
             * @param {String} email
             * @returns {{
             *      street_address: String,
             *      country: String,
             *      city: String,
             *      phone: String,
             *      organization_name: String,
             *      given_name: String,
             *      postal_code: String,
             *      family_name: String,
             *      email: *
             * }}
             */
            buildAddress: function (address, email) {
                var addr = {
                    'organization_name': '',
                    'given_name': '',
                    'family_name': '',
                    'street_address': '',
                    'city': '',
                    'postal_code': '',
                    'country': '',
                    'phone': '',
                    'email': email
                };

                if (!address) { // Somehow we got a null passed in
                    return addr;
                }

                if (address.prefix) {
                    addr.title = address.prefix;
                }

                if (address.firstname) {
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    addr.given_name = address.firstname;
                }

                if (address.lastname) {
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    addr.family_name = address.lastname;
                }

                if (address.street) {
                    if (address.street.length > 0) {
                        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                        addr.street_address = address.street[0];
                    }

                    if (address.street.length > 1) {
                        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                        addr.street_address2 = address.street[1];
                    }
                }

                if (address.city) {
                    addr.city = address.city;
                }

                if (address.regionCode) {
                    addr.region = address.regionCode;
                }

                if (address.postcode) {
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    addr.postal_code = address.postcode;
                }

                if (address.countryId) {
                    addr.country = address.countryId;
                }

                if (address.telephone) {
                    addr.phone = address.telephone;
                }

                // Having organization_name in the billing address causes KP/PLSI to return B2B methods
                // no matter the customer type. So we only want to set this if the merchant has enabled B2B.
                if (address.company && this.b2bEnabled) {
                    addr['organization_name'] = address.company;
                }
                debug.log(addr);

                return addr;
            },

            /**
             * Getting back the customer
             * @param {Object} billingAddress
             * @returns {{type: String}}
             */
            buildCustomer: function (billingAddress) {
                var type = 'person';

                if (this.b2bEnabled && billingAddress && billingAddress.company) {
                    type = 'organization';
                }

                return {
                    'type': type
                };
            },

            /**
             * Getting back data for performing a Klarna update request
             * @returns {{billing_address: {}, shipping_address: {}, customer: {}}}
             */
            getUpdateData: function () {
                var email = '',
                    shippingAddress = quote.shippingAddress(),
                    data = {
                        'billing_address': {},
                        'shipping_address': {},
                        'customer': {}
                    },
                    customFormSelector = '.payment-method.klarna-payments-method._active .billing-address-form form',
                    billingAddress;

                if (customer.isLoggedIn()) {
                    email = customer.customerData.email;
                } else {
                    email = quote.guestEmail;
                }

                if (quote.isVirtual()) {
                    shippingAddress = quote.billingAddress();
                }

                customFormSelector = '.payment-method.klarna-payments-method._active .billing-address-form form';
                billingAddress = this.getBillingAddress(customFormSelector);

                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                data.billing_address = this.buildAddress(billingAddress, email);
                data.shipping_address = this.buildAddress(shippingAddress, email);
                if (!!data.billing_address && !!data.shipping_address) {
                    data.shipping_address.organization_name = data.billing_address.organization_name;
                }
                data.customer = this.buildCustomer(billingAddress);
                debug.log(data);

                return data;
            },

            /**
             * Performing the Klarna load request to load the Klarna widget
             * @param {String} paymentMethod
             * @param {String} containerId
             * @param {Callback} callback
             */
            load: function (paymentMethod, containerId, callback) {
                var data = null;

                debug.log('Loading container ' + containerId);

                if ($('#' + containerId).length) {
                    debug.log('Loading method ' + paymentMethod);

                    if (config.dataSharingOnload) {
                        data = this.getUpdateData();
                    }

                    if (config.isKecSession) {
                        paymentMethod = null;
                    }

                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    Klarna.Payments.load(
                        {
                            payment_method_category: paymentMethod,
                            container: '#' + containerId
                        },
                        data,
                        function (res) {
                            var errors = false;

                            debug.log(res);

                            if (res.errors) {
                                errors = true;
                            }
                            config.hasErrors(errors);

                            if (callback) {
                                callback(res);
                            }
                        }
                    );
                }
            },

            /**
             * Initiating Klarna to add the javascript SDK to the page
             */
            init: function () {
                // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                Klarna.Payments.init({
                    client_token: config.clientToken()
                });
            },

            /**
             * Sending the Klarna authorize request
             * @param {String} paymentMethod
             * @param {Array} data
             * @param {Callback} callback
             */
            authorize: function (paymentMethod, data, callback) {
                if (config.isKecSession) {
                    Klarna.Payments.finalize({}, data, function (res) {
                        var errors = false;

                        if (res.errors) {
                            errors = true;
                        }
                        config.hasErrors(errors);
                        callback(res);
                    });
                } else {

                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    Klarna.Payments.authorize(
                        {
                            payment_method_category: paymentMethod
                        },
                        data,
                        function (res) {
                            var errors = false;

                            debug.log(res);

                            if (res.errors) {
                                errors = true;
                            }
                            config.hasErrors(errors);
                            callback(res);
                        }
                    );
                }
            },

            /**
             * Get the billingAddress value
             */
            getBillingAddress: function (formSelector) {
                var billingAddressForm,
                    billingAddressData;

                if (isBillingSameAsShipping) {
                    return quote.shippingAddress();
                }

                if (!this.addressIsEmpty(quote.billingAddress())) {
                    return quote.billingAddress();
                }

                billingAddressForm = this.detectBillingAddressForm(formSelector);
                billingAddressData = this.getBillingAddressFormData(billingAddressForm);

                if (this.addressIsEmpty(billingAddressData)) {
                    return quote.shippingAddress();
                }

                return createBillingAddress(billingAddressData);
            },

            /**
             * Select billing address form and return the form
             * @param formSelector
             * @returns {jQuery|HTMLElement|*}
             */
            detectBillingAddressForm: function (formSelector) {
                var defaultMagentoFormSelector = '.billing-address-form form',
                    form = $(formSelector);

                if (form.length > 0) {
                    return form;
                }

                return $(defaultMagentoFormSelector);
            },

            /**
             * Extract data from the html form element
             * @param billingAddressForm
             * @returns {*}
             */
            getBillingAddressFormData: function (billingAddressForm) {
                var fields = $(billingAddressForm).serializeArray();

                // create address object from array
                return fields.reduce(function (result, field) {
                    var name = field.name,
                        value = field.value,
                        // select `address[0]` and remove the `[0]` part
                        selectCounterBracketRegex = /\[\d+\]/g;

                    if (selectCounterBracketRegex.test(name)) {
                        name = name.replace(selectCounterBracketRegex, '');
                        value = result[name] && Array.isArray(result[name]) ? result[name] : [];
                        value.push(field.value);
                    }

                    result[name] = value;

                    return result;
                }, {});
            },

            /**
             * check if the address is empty or not
             * @param address
             * @returns {this is string[]}
             */
            addressIsEmpty: function (address) {
                var properties = [
                    'city',
                    'company',
                    'firstname',
                    'lastname',
                    'postcode',
                    'street',
                    'telephone'
                ];

                if (!address) {
                    return true;
                }

                return properties.every(function (propertyName) {
                    return !address[propertyName];
                });
            }
        };
    }
);
