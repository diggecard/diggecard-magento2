<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Plugin\Cart;

use Diggecard\Giftcard\Model\Product\Type\Giftcard;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable;
use Magento\Catalog\Block\Product\Image;
use Magento\Quote\Model\Quote\Item\Option;

/**
 * Class CartImage
 *
 * @package Diggecard\Giftcard\Plugin\Cart
 */
class CartImage
{
    /**
     * @param Configurable $rendererItem
     * @param Image $result
     * @return mixed
     */
    public function afterGetImage($rendererItem, $result)
    {
        /** @var CartItemInterface  $item */
        $item = $rendererItem->getItem();
        $productType = $item->getProductType();
        if($productType == Giftcard::TYPE_CODE) {
            /** @var Option $imageUrl */
            $imageUrl = $item->getOptionByCode('dg_giftcard_image');
            if ($imageUrl){
                $result->setImageUrl($imageUrl->getValue());
            }
        }
        return $result;
    }
}