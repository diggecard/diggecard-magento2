<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Observer\Cart;

use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Diggecard\Giftcard\Model\Product\Type\Giftcard;
use Magento\Quote\Api\Data\ProductOptionInterface;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;

/**
 * Class CustomPrice
 *
 * @package Diggecard\Giftcard\Observer\Cart
 */
class CustomPrice implements ObserverInterface
{

    /**
     * @var CustomOptionFactory
     */
    protected $customOptionFactory;

    /**
     * @var ProductOptionInterfaceFactory
     */
    protected $productOptionInterfaceFactory;

    /**
     * @var \Magento\Quote\Api\Data\ProductOptionExtensionFactory
     */
    protected $extensionFactory;

    /**
     * CustomPrice constructor.
     * @param CustomOptionFactory $customOptionFactory
     * @param ProductOptionInterfaceFactory $productOptionInterfaceFactory
     * @param \Magento\Quote\Api\Data\ProductOptionExtensionFactory $extensionFactory
     */
    public function __construct(
        CustomOptionFactory $customOptionFactory,
        ProductOptionInterfaceFactory $productOptionInterfaceFactory,
        \Magento\Quote\Api\Data\ProductOptionExtensionFactory $extensionFactory
    ) {
        $this->customOptionFactory = $customOptionFactory;
        $this->productOptionInterfaceFactory = $productOptionInterfaceFactory;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer) {
        /** @var CartItemInterface $item */
        $item = $observer->getEvent()->getData('quote_item');
        $product = $observer->getEvent()->getData('product');

        if ($product->getTypeId() == Giftcard::TYPE_CODE) {
            $productCustomOptions = $product->getCustomOptions();

            $optionData = $this->getCustomOptionData($productCustomOptions);
            $productOptionInterface = $this->productOptionInterfaceFactory->create();
            $productOption = $this->setProductOptionExtensionAttribute($productOptionInterface, $optionData);
            $item->setProductOption($productOption);

            $price = $product->getCustomOption('dg_giftcard_value')->getValue();
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }
    }

    /**
     * @param $productCustomOptions
     * @return array
     */
    private function getCustomOptionData($productCustomOptions) {
        $keys = ['dg_giftcard_image', 'dg_giftcard_value', 'dg_giftcard_hash'];
        $options = [];
        foreach ($keys as $key) {
            $options[] = [
                "option_id" => $key,
                "option_value" => $productCustomOptions[$key]->getValue()
            ];
        }

        $customOption = $this->customOptionFactory->create();

        $optionData = [];
        foreach ($options as $option) {
            $optionData[] = $customOption->setData($option);
        }

        return $optionData;
    }

    /**
     * @param ProductOptionInterface $productOption
     * @param array $optionData
     * @return ProductOptionInterface
     */
    private function setProductOptionExtensionAttribute($productOption, $optionData) {
        $extAttribute = $productOption->getExtensionAttributes()
            ? $productOption->getExtensionAttributes()
            : $this->extensionFactory->create();
        $extAttribute->setCustomOptions($optionData);
        $productOption->setExtensionAttributes($extAttribute);

        return $productOption;
    }

}