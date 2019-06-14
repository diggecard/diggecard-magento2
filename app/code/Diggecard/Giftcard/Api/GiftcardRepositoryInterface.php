<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Api;

use Zend\Db\Sql\Ddl\Column\Timestamp;
use Diggecard\Giftcard\Api\Data\GiftcardInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface CityRepositoryInterface
 *
 * @package Diggecard\Giftcard\Api
 */
interface GiftcardRepositoryInterface
{
    /**
     * @param int $entityId
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * @param $qrCode
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByQrCode($qrCode);

    /**
     * @param $cardData
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCardData($cardData);

    /**
     * @param $valueRemains
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByValueRemains($valueRemains);

    /**
     * @param Timestamp
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCreatedAt($createdAt);

    /**
     * @param Timestamp
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByUpdatedAt($updatedAt);

    /**
     * @param Timestamp
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByValidUntil($validUntil);

    /**
     * @param GiftcardInterface $giftcard
     *
     * @return GiftcardInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(GiftcardInterface $giftcard);

    /**
     * @param GiftcardInterface $giftcard
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(GiftcardInterface $giftcard);

    /**
     * @param integer $entityId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByEntityId($entityId);

    /**
     * @param string $qrCode
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByQrCode($qrCode);

    /**
     * @param string $cardData
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByCardData($cardData);

    /**
     * @param string $valueRemains
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByValueRemains($valueRemains);

    /**
     * @param Timestamp $createdAt
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByCreatedAt($createdAt);

    /**
     * @param Timestamp $updatedAt
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByUpdatedAt($updatedAt);

    /**
     * @param Timestamp $validUntil
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByValidUntil($validUntil);

}