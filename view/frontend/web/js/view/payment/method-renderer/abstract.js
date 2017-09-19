/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Kevwis_Atos/js/action/set-payment-method',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/customer-data'
    ],
    function (
        $,
        Component,
        setPaymentMethodAction,
        additionalValidators,
        quote
    ) {

        'use strict';
        return Component.extend({

            defaults: {
                redirectAfterPlaceOrder: false,
                billingAgreement: '',
                timeoutMessage: 'Sorry, but something went wrong. Please contact the seller.'
            },


            /** Init observable variables */
            initObservable: function () {
                this._super().observe([
                    'billingAgreement'
                ]);

                return this;
            },

            /** Open window with  */
            showAcceptanceWindow: function (data, event) {
                window.open(
                    $(event.target).attr('href'),
                    'atosacceptance',
                    'toolbar=no, location=no,' +
                    ' directories=no, status=no,' +
                    ' menubar=no, scrollbars=yes,' +
                    ' resizable=yes, ,left=0,' +
                    ' top=0, width=400, height=350'
                );

                return false;
            },

            /** Returns payment acceptance mark link path */
            getAvailableCardTypes: function () {
                return this.getAvailableCardTypeValues(this.getCode());
            },

            /** Returns payment acceptance mark link path */
            getAvailableCardTypeValues: function (method) {
                return window.checkoutConfig.payment.atos.availableCardTypes[method];
            },

            getIcon: function (type) {
                 return window.checkoutConfig.payment.atos.icons.hasOwnProperty(type.toUpperCase()) ?
                    window.checkoutConfig.payment.atos.icons[type.toUpperCase()]
                    : false;
            },

            /** Returns payment acceptance mark link path */
            getPaymentAcceptanceMarkHref: function () {
                return window.checkoutConfig.payment.atos.paymentAcceptanceMarkHref;
            },

            /** Returns payment acceptance mark image path */
            getPaymentAcceptanceMarkSrc: function () {
                return window.checkoutConfig.payment.atos.paymentAcceptanceMarkSrc;
            },

            /** Returns billing agreement data */
            getBillingAgreementCode: function () {
                return window.checkoutConfig.payment.atos.billingAgreementCode.default;
            },

            /** Returns payment information data */
            getData: function () {

                var parent = this._super(),
                    additionalData = {};

                if (this.getBillingAgreementCode()) {
                    additionalData[this.getBillingAgreementCode()] = this.billingAgreement();
                }

                additionalData['cc_type'] = $('input[name="payment[cc_type]"]:checked').val();

                return $.extend(true, parent, {
                    'additional_data': additionalData
                });
            },

            /**
             * After place order callback
             */
            afterPlaceOrder: function () {
                $.mage.redirect(
                    window.checkoutConfig.payment.atos.redirectUrl[quote.paymentMethod().method]
                );
            },
        });
    }
);
