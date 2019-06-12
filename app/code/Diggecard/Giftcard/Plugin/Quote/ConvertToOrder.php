<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Plugin\Quote;

use Diggecard\Giftcard\Helper\Data as Json;
use Closure;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Model\Order\Item;

/**
 * Class ConvertToOrder
 *
 * @package Diggecard\Giftcard\Plugin\Quote
 */
class ConvertToOrder
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * ConvertToOrder constructor.
     * @param Json $json
     */
    public function __construct(
      Json $json
    ) {
        $this->json = $json;
    }

    /**
     * @param ToOrderItem $subject
     * @param Closure $proceed
     * @param AbstractItem $item
     * @param array $additional
     * @return Item
     */
    public function aroundConvert(
        ToOrderItem $subject,
        Closure $proceed,
        AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem Item */
        $orderItem = $proceed($item, $additional);
        $keys = [
            'dg_giftcard_value',
            'dg_giftcard_hash',
            'dg_giftcard_image'
        ];
        $productOptions = $orderItem->getProductOptions();
        $customOptions = [];

        if (is_array($item->getOptions())) {
            foreach ($item->getOptions() as $key => $itemOption) {
                if ($itemOption->getCode() == 'info_buyRequest' && $options = $itemOption->getValue()) {
                    try{
                        $customOptions =  $this->json->unserialize($options);
                    } catch (\Exception $e) {
                        $customOptions =  unserialize($options);
                    }
                }
            }
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $customOptions)) {
                $productOptions[$key] = $customOptions[$key];
            }
        }

        $orderItem->setProductOptions($productOptions);
        return $orderItem;
    }
}
