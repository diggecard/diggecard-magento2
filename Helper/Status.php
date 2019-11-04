<?php

namespace Diggecard\Giftcard\Helper;

use Diggecard\Giftcard\Controller\Giftcard\Add;
use Diggecard\Giftcard\Model\ResourceModel\Giftcard;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductSourceStatus;

class Status
{
    /** @var ResourceConnection */
    private $resourseConnection;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var StockItemRepository */
    protected $stockRepository;

    /**
     * Status constructor.
     * @param ResourceConnection $resourceConnection
     * @param ProductRepositoryInterface $productRepository
     * @param StockItemRepository $stockRepository
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ProductRepositoryInterface $productRepository,
        StockItemRepository $stockRepository
    )
    {
        $this->resourseConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->product = $product;
        $this->stockRepository = $stockRepository;
    }

    /**
     * @return bool
     */
    public function isTableExists()
    {
        return $this->resourseConnection->getConnection()->isTableExists(Giftcard::TABLE_NAME);
    }

    /**
     * @return bool
     */
    public function isProductExists()
    {
        if ($this->getProduct())
            return true;

        return false;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isProductSalable()
    {
        $productStatus = $this->getProduct()->getStatus();
        $stockStatus = $this->stockRepository->get($this->getProduct()->getId())->getIsInStock();

        if ($productStatus == ProductSourceStatus::STATUS_ENABLED && $stockStatus)
            return true;

        return false;
    }

    /**
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProduct()
    {
        return $this->productRepository->get(Add::DG_SKU, false, 0);
    }
}