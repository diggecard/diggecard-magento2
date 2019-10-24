<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
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
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function get($entityId);

    /**
     * @param $qrCode
     *
     * @return GiftcardInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByQrCode($qrCode);

    /**
     * @param $cardData
     *
     * @return GiftcardInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByCardData($cardData);

    /**
     * @param $valueRemains
     *
     * @return GiftcardInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByValueRemains($valueRemains);

    /**
     * @param Timestamp
     *
     * @return GiftcardInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByCreatedAt($createdAt);

    /**
     * @param Timestamp
     *
     * @return GiftcardInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByUpdatedAt($updatedAt);

    /**
     * @param Timestamp
     *
     * @return GiftcardInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByValidUntil($validUntil);

    /**
     * @param GiftcardInterface $giftcard
     *
     * @return GiftcardInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(GiftcardInterface $giftcard);

    /**
     * @param GiftcardInterface $giftcard
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     */
    public function delete(GiftcardInterface $giftcard);

    /**
     * @param integer $entityId
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteByEntityId($entityId);

    /**
     * @param string $qrCode
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteByQrCode($qrCode);

    /**
     * @param string $cardData
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteByCardData($cardData);

    /**
     * @param string $valueRemains
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteByValueRemains($valueRemains);

    /**
     * @param Timestamp $createdAt
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteByCreatedAt($createdAt);

    /**
     * @param Timestamp $updatedAt
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteByUpdatedAt($updatedAt);

    /**
     * @param Timestamp $validUntil
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteByValidUntil($validUntil);

}