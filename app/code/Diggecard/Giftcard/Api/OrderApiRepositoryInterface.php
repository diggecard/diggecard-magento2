<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Api;

/**
 * Interface GiftcardOrderApiRepository
 *
 * @package Diggecard\Giftcard\Api
 */
interface OrderApiRepositoryInterface
{

    /**
     *  Method API: Get order by hash
     */
    const GET_ORDER_HASH = '/order/%s';

    /**
     *  Method API: Complete an order
     */
    const POST_ORDER_COMPLETE = '/order/complete';

    /**
     * @param string $hash
     * @return mixed
     * {
     *   'orderHash' => string 'kYmkjzzr41'
     *   'status' => string 'NEW'
     *   'createdTime' => int
     *   'numberOfGiftCards' => int
     *   'totalOrderFee' => float
     *   'amountGross' => int
     *   'amountNet' => int
     *   'currency' => string 'NOK'
     *   'mediaUrl' => string ''
     *   'orderInfo' => null
     *   'giftCards' => array (size=0)
     *   }
     */
    public function getOrderByHash($hash);

    /**
     * @param array $data
     *   { "orderHash"
     *     "firstName"
     *     "lastName"
     *     "email"
     *     "externalOrderId" }
     *
     * @return mixed
     */
    public function postCompleteOrder($data);
}