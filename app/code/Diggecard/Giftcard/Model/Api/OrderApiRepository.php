<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model\Api;

use Diggecard\Giftcard\Api\OrderApiRepositoryInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Diggecard\Giftcard\Helper\Data as Json;
use Diggecard\Giftcard\Model\Config;

/**
 * Class OrderApiRepository
 *
 * @package Diggecard\Giftcard\Model\Api
 */
class OrderApiRepository extends GiftcardApi implements OrderApiRepositoryInterface
{
    /**
     * OrderApiRepository constructor.
     * @param CurlFactory $curlFactory
     * @param Json $json
     * @param Config $config
     */
    public function __construct(
        CurlFactory $curlFactory,
        Json $json,
        Config $config
    ) {
        parent::__construct(
            $curlFactory,
            $json,
            $config
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderByHash($hash)
    {
        return $this->get(OrderApiRepositoryInterface::GET_ORDER_HASH, $hash);
    }

    /**
     * {@inheritDoc}
     */
    public function postCompleteOrder($data)
    {
        return $this->post(OrderApiRepositoryInterface::POST_ORDER_COMPLETE, $data);
    }
}