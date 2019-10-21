<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model;

use Zend\Db\Sql\Ddl\Column\Timestamp;
use Magento\Framework\Model\AbstractModel;
use Diggecard\Giftcard\Api\Data\GiftcardInterface;

/**
 * Class Giftcard
 *
 * @package Diggecard\Giftcard\Model
 */
class Giftcard extends AbstractModel implements GiftcardInterface
{
    /**
     * Set resource model
     */
    protected function _construct()
    {
        $this->_init('Diggecard\Giftcard\Model\ResourceModel\Giftcard');
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(GiftcardInterface::ENTITY_ID);
    }

    /**
     * @return string
     */
    public function getQrCode()
    {
        return $this->getData(GiftcardInterface::QR_CODE);
    }

    /**
     * @return string
     */
    public function getCardData()
    {
        return $this->getData(GiftcardInterface::CARD_DATA);
    }

    /**
     * @return string
     */
    public function getValueRemains()
    {
        return $this->getData(GiftcardInterface::VALUE_REMAINS);
    }

    /**
     * @return string
     */
    public function getBaseValueRemains()
    {
        return $this->getData(GiftcardInterface::BASE_VALUE_REMAINS);
    }

    /**
     * @return Timestamp
     */
    public function getCreatedAt()
    {
        return $this->getData(GiftcardInterface::CREATED_AT);
    }

    /**
     * @return Timestamp
     */
    public function getUpdatedAt()
    {
        return $this->getData(GiftcardInterface::UPDATED_AT);
    }

    /**
     * @return Timestamp
     */
    public function getValidUntil()
    {
        return $this->getData(GiftcardInterface::VALID_UNTIL);
    }

    /**
     * @param $id
     *
     * @return GiftcardInterface
     */
    public function setEntityId($id)
    {
        return $this->setData(GiftcardInterface::ENTITY_ID, $id);
    }

    /**
     * @param string $qrCode
     *
     * @return GiftcardInterface
     */
    public function setQrCode($qrCode)
    {
        return $this->setData(GiftcardInterface::QR_CODE, $qrCode);
    }

    /**
     * @param string $cardData
     *
     * @return GiftcardInterface
     */
    public function setCardData($cardData)
    {
        return $this->setData(GiftcardInterface::CARD_DATA, $cardData);
    }

    /**
     * @param string $valueRemains
     *
     * @return GiftcardInterface
     */
    public function setValueRemains($valueRemains)
    {
        return $this->setData(GiftcardInterface::VALUE_REMAINS, $valueRemains);
    }

    /**
     * @param string $baseValueRemains
     *
     * @return GiftcardInterface
     */
    public function setBaseValueRemains($baseValueRemains)
    {
        return $this->setData(GiftcardInterface::BASE_VALUE_REMAINS, $baseValueRemains);
    }

    /**
     * @param Timestamp $createdAt
     *
     * @return GiftcardInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(GiftcardInterface::CREATED_AT, $createdAt);
    }

    /**
     * @param Timestamp $updatedAt
     *
     * @return GiftcardInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(GiftcardInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * @param Timestamp $validUntil
     *
     * @return GiftcardInterface
     */
    public function setValidUntil($validUntil)
    {
        return $this->setData(GiftcardInterface::VALID_UNTIL, $validUntil);
    }

}
