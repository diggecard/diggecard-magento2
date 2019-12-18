define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'mage/url',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/totals',
    ],
    function ($, ko, Component, quote, url, getTotalsAction, customerData, totals) {
        "use strict";
        return Component.extend({
            giftcardValid: ko.observable(false),
            diggecardIsEnable: ko.observable(window.checkoutConfig.diggecard.isEnable),
            giftcardQrCode: ko.observable(window.checkoutConfig.diggecard.giftcard.qrCode),
            giftcardValueRemains: ko.observable(window.checkoutConfig.diggecard.giftcard.valueRemains),
            currentCurrency: ko.observable(window.checkoutConfig.diggecard.giftcard.currencyCode),
            qrInvalidError: ko.observable(false),
            cardViaCardError: ko.observable(false),
            emptyGiftCard: ko.observable(false),
            noSuchGiftCard: ko.observable(false),
            noGiftCardEntered: ko.observable(false),
            totals: quote.getTotals(),

            defaults: {
                template: 'Diggecard_Giftcard/checkout/cart/giftcard'
            },

            initialize: function(){
                var self = this;
                self._super();
                self.giftcardValid(self.giftcardQrCode() !== undefined);
            },

            getGiftcardDefaultCode : function(){
                return '';
            },

            getGiftcardQrCode: function(){
                return this.giftcardQrCode();
            },

            getGiftcardValueRemains: function(){
                return this.giftcardValueRemains();
            },

            getCurrentCurrency: function(){
                return this.currentCurrency();
            },

            setGiftcardData: function(ajaxData){
                this.giftcardQrCode(ajaxData.giftcardQrCode);
                this.giftcardValueRemains(ajaxData.giftcardValueRemains);
                this.currentCurrency(ajaxData.currentCurrency)
            },

            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('diggecard_giftcard_discount').value;
                }
                return this.getFormattedPrice(price);
            },

            getTitle: function() {
                return totals.getSegment('diggecard_giftcard_discount').title;
            },

            setDiscountValue: function(){
                var self = this;
                var linkUrl = url.build('diggecard/checkout/apply');
                console.log(linkUrl);
                var param = {
                    qrCode: $('input#giftcard_code').val()
                };

                $.ajax({
                    showLoader: true,
                    url: linkUrl,
                    data: param,
                    type: "POST",
                    dataType: 'json'
                }).done(function (ajaxData) {
                    self.receiveSetDiscountData(ajaxData);
                    self.updateCartTotals();
                });
            },

            removeGiftcardDiscount: function(){
                var self = this;
                var linkUrl = url.build('diggecard/checkout/cancel');
                console.log(linkUrl);

                var param = {
                    qrCode: self.giftcardQrCode()
                };

                $.ajax({
                    showLoader: true,
                    url: linkUrl,
                    data: param,
                    type: "POST",
                    dataType: 'json'
                }).done(function (ajaxData) {
                    self.receiveRemoveDiscountData(ajaxData);
                });
            },

            receiveSetDiscountData: function(ajaxData) {
                var self = this;

                console.log(ajaxData);
                if (ajaxData.valid) {
                    self.setGiftcardData(ajaxData);
                    self.giftcardValid(true);
                    self.qrInvalidError(false);
                    self.cardViaCardError(false);
                    self.emptyGiftCard(false);
                } else if (ajaxData.error_type == '1'){
                    self.qrInvalidError(true);
                } else if (ajaxData.error_type == '2'){
                    self.cardViaCardError(true);
                } else if (ajaxData.error_type == '3'){
                    self.emptyGiftCard(true);
                }
            },

            updateCartTotals: function () {
                var sections = ['cart'];
                customerData.reload(sections, true);
                var deferred = $.Deferred();
                getTotalsAction([], deferred);
            },

            receiveRemoveDiscountData: function(ajaxData) {
                console.log(ajaxData);
                this.giftcardValid(false);
                this.updateCartTotals();
                $('input#giftcard_code').val('');
            },

            templateVarsCheck: function(){
                var self = this;

                if (self.giftcardQrCode.length > 0 && self.giftcardValueRemains !== undefined) {
                    self.templateError(false);
                }
            }
        });
    }
);
