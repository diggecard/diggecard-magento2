<?php

namespace Diggecard\Giftcard\Helper;

use Diggecard\Giftcard\Model\Config;
use Magento\Sales\Api\Data\OrderInterface;

class Hash
{
    /** @var Config */
    protected $_config;

    public function __construct(
        Config $config
    )
    {
        $this->_config = $config;
    }

    /**
     * @param $order OrderInterface
     * @return string
     */
    public function generateHash($order)
    {
        $string =
            $this->_config->getApiKey()
            . $order->getEntityId()
            . $order->getCustomerEmail()
            . $order->getGrandTotal()
            . $order->getCreatedAt();

        return hash('sha256', $string);
    }
}
