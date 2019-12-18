define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, ko, Component, quote, totals, priceUtils) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Diggecard_Giftcard/checkout/summary/diggecard-giftcard'
            },

            diggecardIsEnable: ko.observable(window.checkoutConfig.diggecard.isEnable),
            giftcardQrCode: ko.observable(window.checkoutConfig.diggecard.giftcard.qrCode),
            totals: quote.getTotals(),

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

            getGiftcardQrCode: function(){
                return this.giftcardQrCode();
            },

            isDiggecardEnable: function(){
                return this.diggecardIsEnable();
            },
        });
    }
);
