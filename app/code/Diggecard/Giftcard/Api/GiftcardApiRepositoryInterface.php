<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Api;

/**
 * Interface GiftcardApiRepositoryInterface
 * @package Diggecard\Giftcard\Api
 */
interface GiftcardApiRepositoryInterface
{
    /**
     * API Method , getGiftCard by QrCode
     */
    const GET_GIFTCARD_QR_CODE = '/giftcard/qrcode/';

    /**
     * API Method Giftcard Issue
     */
    const POST_GIFTCARD_ISSUE = '/giftcard/issue';

    /**
     * API Method Reserve amount from a gift card
     */
    const POST_GIFTCARD_RESERVE = '/giftcard/reserve';

    /**
     * API Method Capture a reserved amount from a gift card
     */
    const POST_GIFTCARD_CAPTURE = '/giftcard/capture';

    /**
     * @param string $qrCode
     * @return array|mixed
     * {
     *   "externalMerchantId": "",
     *   "currencyCode": "DigCurrency",
     *   "id": 0,
     *   "merchantId": 0,
     *   "externalId": "",
     *   "cardGivenByUserName": "",
     *   "deliverAtTime": "",
     *   "resourceType": "ResourceType",
     *   "createdTime": "",
     *   "giftCardOfferId": 0,
     *   "deliveryFee": 0,
     *   "phoneNumberFullReceiver": "",
     *   "qrCode": "",
     *   "nameReceiver": "",
     *   "valueRemains": 0,
     *   "valueInitial": 0,
     *   "giftCardStatus": "GiftCardStatus",
     *   "deliveryStatus": "DeliveryStatus",
     *   "messageToReceiver": "",
     *   "validUntilTime": "",
     *   "resourceUrl": ""
     *   }
     */
    public function getGiftCardByQrCode($qrCode);

    /**
     * @param array $data
     *  { "value": 0,
     *    "merchantId": "",
     *    "giftCardOfferId": 0,
     *    "qrCode": "",
     *    "nameReceiver": "",
     *    "phoneNumberReceiver": "",
     *    "externalGiftCardId": "",
     *    "posId": "",
     *    "phoneNumberBuyer": "",
     *    "nameBuyer": "",
     *    "retailStoreId": ""  }
     *
     * @return mixed
     *  {
     *    "externalMerchantId": "",
     *    "currencyCode": "DigCurrency",
     *    "id": 0,
     *    "merchantId": 0,
     *    "externalId": "",
     *    "cardGivenByUserName": "",
     *    "deliverAtTime": "",
     *    "resourceType": "ResourceType",
     *    "createdTime": "",
     *    "giftCardOfferId": 0,
     *    "deliveryFee": 0,
     *    "phoneNumberFullReceiver": "",
     *    "qrCode": "",
     *    "nameReceiver": "",
     *    "valueRemains": 0,
     *    "valueInitial": 0,
     *    "giftCardStatus": "GiftCardStatus",
     *    "deliveryStatus": "DeliveryStatus",
     *    "messageToReceiver": "",
     *    "validUntilTime": "",
     *    "resourceUrl": ""
     *    }
     */
    public function postGiftcardIssue($data);

    /**
     * @param array $data
     * {  "merchantId"
     *    "qrCode"
     *    "amount"
     *     "minutesToReserve"  }
     * @return mixed
     * {
     *   "externalMerchantId": "",
     *   "currencyCode": "DigCurrency",
     *   "id": 0,
     *   "merchantId": 0,
     *   "externalId": "",
     *   "cardGivenByUserName": "",
     *   "deliverAtTime": "",
     *   "resourceType": "ResourceType",
     *   "createdTime": "",
     *   "giftCardOfferId": 0,
     *   "deliveryFee": 0,
     *   "phoneNumberFullReceiver": "",
     *   "qrCode": "",
     *   "nameReceiver": "",
     *   "valueRemains": 0,
     *   "valueInitial": 0,
     *   "giftCardStatus": "GiftCardStatus",
     *   "deliveryStatus": "DeliveryStatus",
     *   "messageToReceiver": "",
     *   "validUntilTime": "",
     *   "resourceUrl": ""
     *   }
     */
    public function postReserveGiftcardAmount($data);

    /**
     * @param array $data
     *  {  "reservationCode"
     *     "merchantId"
     *     "qrCode"
     *     "amount"  }
     * @return mixed
     *  {
     *  "externalMerchantId": "",
     *  "currencyCode": "DigCurrency",
     *  "id": 0,
     *  "merchantId": 0,
     *  "externalId": "",
     *  "cardGivenByUserName": "",
     *  "deliverAtTime": "",
     *  "resourceType": "ResourceType",
     *  "createdTime": "",
     *  "giftCardOfferId": 0,
     *  "deliveryFee": 0,
     *  "phoneNumberFullReceiver": "",
     *  "qrCode": "",
     *  "nameReceiver": "",
     *  "valueRemains": 0,
     *  "valueInitial": 0,
     *  "giftCardStatus": "GiftCardStatus",
     *  "deliveryStatus": "DeliveryStatus",
     *  "messageToReceiver": "",
     *  "validUntilTime": "",
     *  "resourceUrl": ""
     *  }
     */
    public function postCaptureReservedGiftcardAmount($data);
}