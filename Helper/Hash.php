<?php

namespace Diggecard\Giftcard\Helper;

use Diggecard\Giftcard\Model\Config;
use Magento\Sales\Api\Data\OrderInterface;

class Hash
{
    /** @var Config */
    protected $config;

    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * @param $order OrderInterface
     * @return string
     */
    public function generateHash($order)
    {
        $string =
            $this->config->getApiKey()
            . $order->getEntityId()
            . $order->getCustomerEmail()
            . $order->getGrandTotal()
            . $order->getCreatedAt();

        return hash('sha256', $string);
    }
}
