<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;
use Magento\Quote\Api\Data\ProductOptionInterface;

use Diggecard\Giftcard\Helper\Data as Json;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Framework\Event\ManagerInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Option;
use Magento\Eav\Model\Config;

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

    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        $product->addCustomOption('dg_giftcard_image', $buyRequest->getDgGiftcardImage(), $product);
        $product->addCustomOption('dg_giftcard_value', $buyRequest->getDgGiftcardValue(), $product);
        $product->addCustomOption('dg_giftcard_hash', $buyRequest->getDgGiftcardHash(), $product);
        $product->setName($buyRequest->getName());
        $this->productRepository->save($product);

        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }
        return $result;
    }
}
