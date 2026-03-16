/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */


define(['ko', 'Kartbutikken_NordecakonsumentOrderAttributes/js/checkout-payment-data'], function (ko, paymentData) {
        'use strict';

        let data = paymentData.getPaymentFromData(),
            orderAttributes = window.order_attributes,
            useInvoiceEmail = data ? data['use_invoice_email'] : (orderAttributes ? orderAttributes.use_invoice_email : false),
            invoiceEmail = data ? data['invoice_email'] : (orderAttributes ? orderAttributes.invoice_email : null),
            referenceCode = data ? data['reference_code'] : null;

        return {
            use_invoice_email: ko.observable(useInvoiceEmail),
            invoice_email: ko.observable(invoiceEmail),
            reference_code: ko.observable(referenceCode),

            getData: function () {
                return {
                    use_invoice_email: this.use_invoice_email(),
                    invoice_email: this.invoice_email(),
                    reference_code: this.reference_code()
                }
            }
        }
    }
);