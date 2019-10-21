<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Diggecard\Giftcard\Api\Data\GiftcardInterface;
use Diggecard\Giftcard\Api\Data\GiftcardSearchResultsInterfaceFactory;
use Diggecard\Giftcard\Model\GiftcardFactory;
use Diggecard\Giftcard\Model\ResourceModel\Giftcard as ResourceModel;
use Diggecard\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory;

/**
 * Class GiftcardRepository
 *
 * @package Diggecard\Giftcard\Model
 */
class GiftcardRepository implements GiftcardRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resource;

    /**
     * @var GiftcardFactory
     */
    protected $giftcardFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var GiftcardSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * GiftcardRepository constructor.
     * @param ResourceModel $resource
     * @param GiftcardFactory $giftcardFactory
     * @param CollectionFactory $collectionFactory
     * @param GiftcardSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceModel $resource,
        GiftcardFactory $giftcardFactory,
        CollectionFactory $collectionFactory,
        GiftcardSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->giftcardFactory = $giftcardFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($entityId)
    {
        /** @var GiftcardInterface $model */
        $model = $this->giftcardFactory->create();
        $this->resource->load($model, $entityId);
        if (!$model->getEntityId()) {
            throw new NoSuchEntityException(__('Giftcard with Entity Id %1 does not exist.', $entityId));
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function getByQrCode($qrCode)
    {
        /** @var GiftcardInterface $model */
        $model = $this->giftcardFactory->create();
        $this->resource->load($model, $qrCode, GiftcardInterface::QR_CODE);
        if (!$model->getEntityId()) {
            throw new NoSuchEntityException(__('Giftcard with QR Code %1 does not exist.', $qrCode));
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function getByCardData($cardData)
    {
        /** @var GiftcardInterface $model */
        $model = $this->giftcardFactory->create();
        $this->resource->load($model, $cardData, GiftcardInterface::CARD_DATA);
        if (!$model->getEntityId()) {
            throw new NoSuchEntityException(__('Giftcard with Card Data %1 does not exist.', $cardData));
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function getByValueRemains($valueRemains)
    {
        /** @var GiftcardInterface $model */
        $model = $this->giftcardFactory->create();
        $this->resource->load($model, $valueRemains, GiftcardInterface::VALUE_REMAINS);
        if (!$model->getEntityId()) {
            throw new NoSuchEntityException(__('Giftcard with Value Remains %1 does not exist.', $valueRemains));
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function getByCreatedAt($createdAt)
    {
        /** @var GiftcardInterface $model */
        $model = $this->giftcardFactory->create();
        $this->resource->load($model, $createdAt, GiftcardInterface::CREATED_AT);
        if (!$model->getEntityId()) {
            throw new NoSuchEntityException(__('Giftcard with Create At %1 does not exist.', $createdAt));
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function getByUpdatedAt($updatedAt)
    {
        /** @var GiftcardInterface $model */
        $model = $this->giftcardFactory->create();
        $this->resource->load($model, $updatedAt, GiftcardInterface::UPDATED_AT);
        if (!$model->getEntityId()) {
            throw new NoSuchEntityException(__('Giftcard with Update At %1 does not exist.', $updatedAt));
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function getByValidUntil($validUntil)
    {
        /** @var GiftcardInterface $model */
        $model = $this->giftcardFactory->create();
        $this->resource->load($model, $validUntil, GiftcardInterface::VALID_UNTIL);
        if (!$model->getEntityId()) {
            throw new NoSuchEntityException(__('Giftcard with Valid Until %1 does not exist.', $validUntil));
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function save(GiftcardInterface $giftcard)
    {
        try {
            $this->resource->save($giftcard);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $giftcard;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(GiftcardInterface $giftcard)
    {
        try {
            $this->resource->delete($giftcard);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByEntityId($entityId)
    {
        return $this->delete($this->get($entityId));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByQrCode($qrCode)
    {
        return $this->delete($this->getByQrCode($qrCode));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByCardData($cardData)
    {
        return $this->delete($this->getByCardData($cardData));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByValueRemains($valueRemains)
    {
        return $this->delete($this->getByValueRemains($valueRemains));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByCreatedAt($createdAt)
    {
        return $this->delete($this->getByCreatedAt($createdAt));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByUpdatedAt($updatedAt)
    {
        return $this->delete($this->getByUpdatedAt($updatedAt));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByValidUntil($validUntil)
    {
        return $this->delete($this->getByValidUntil($validUntil));
    }
}
