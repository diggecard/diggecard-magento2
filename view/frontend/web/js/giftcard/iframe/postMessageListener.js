define([
    'jquery',
    'mage/url',
    'Magento_Customer/js/customer-data'
], function ($, url, customerData) {
    'use strict';
    $.widget('diggecard.giftcardIframePostMessageListener', {
        addGiftcardToCheckoutUrl: url.build('diggecard/Cart/Add'),

        _create: function () {
            this.init();
        },

        init: function () {
            this.addEvent();
            window.diggecard = {
                widget: this
            };
        },

        addEvent: function () {
            let self = this;
            if (window.addEventListener) {
                window.addEventListener("message", self.onMessage, false);
            } else if (window.attachEvent) {
                window.attachEvent("onmessage", self.onMessage);
            }
        },

        sendGiftcardData: function (orderData) {
            let self = this;
            let postData = JSON.parse(orderData);
            $.ajax({
                showLoader: true,
                url: url.build('diggecard/giftcard/add'),
                data: postData,
                type: "POST",
                dataType: 'json'
            }).done(function (response) {
                var sections = ['cart','customer'];
                customerData.reload(sections, true);
            });
        },

        onMessage: function (response) {
            let orderData = response.data.order;
            if (orderData !== undefined) {
                window.diggecard.widget.sendGiftcardData(orderData)
            } else {
                console.log('Order Data isn\'t received')
            }
        }
    });

    return $.diggecard.giftcardIframePostMessageListener;
});
