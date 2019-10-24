<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Plugin\Cart;

use Diggecard\Giftcard\Model\Product\Type\Giftcard;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\Option;

/**
 * Class MinicartImage
 *
 * @package Diggecard\Giftcard\Plugin\Cart
 */
class MinicartImage
{
    /**
     * @param $subject
     * @param array $proceed
     * @param CartItemInterface $item
     * @return mixed
     */
    public function aroundGetItemData($subject, $proceed, $item)
    {
        $productType = $item->getProductType();
        $result = $proceed($item);
        if ($productType == Giftcard::TYPE_CODE) {
            /** @var Option $imageUrl */
            $imageUrl = $item->getOptionByCode('dg_giftcard_image');
            if ($imageUrl) {
                $result['product_image']['src'] = $imageUrl->getValue();
            }
        }
        return $result;
    }
}