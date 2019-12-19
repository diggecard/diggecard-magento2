<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;

/**
 * Class Giftcard
 *
 * @package Diggecard\Giftcard\Model\Product\Type
 */
class Giftcard extends AbstractType
{
    const TYPE_CODE = 'diggecard_giftcard';

    /**
     * {@inheritdoc}
     */
    public function deleteTypeSpecificData(Product $product)
    {

    }

    /**
     * Check is virtual product
     *
     * @param Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        return true;
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        $product->addCustomOption('dg_giftcard_image', $buyRequest->getDgGiftcardImage(), $product);
        $product->addCustomOption('dg_giftcard_value', $buyRequest->getDgGiftcardValue(), $product);
        $product->addCustomOption('dg_giftcard_hash', $buyRequest->getDgGiftcardHash(), $product);
        $product->setName($buyRequest->getName());

        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }

        return $result;
    }
}
