<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Api\Data;

/**
 * Interface GiftcardApiInterface
 *
 * @package Diggecard\Giftcard\Api\Data
 */
interface GiftcardApiInterface
{

    /**
     * @param string $method
     * @param array|string $param
     * @return mixed
     */
    public function getMethodUrl($method, $param = null);

    /**
     * Add Parameters to Curl request
     * @param array $headers
     * @return $headers
     */
    public function prepareHeaders($headers = null);

    /**
     * @param $method
     * @param $data
     * @return mixed
     */
    public function get($method, $data);

    /**
     * @param $method
     * @param $data
     * @return mixed
     */
    public function post($method, $data);
}