<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Api\Data;

use Zend\Db\Sql\Ddl\Column\Timestamp;

/**
 * Interface Giftcard
 *
 * @package Diggecard\Giftcard\Api\Data
 */
interface GiftcardInterface
{
    /**
     *  Table column [string] (PK)
     */
    const ENTITY_ID = 'entity_id';

    /**
     *  Table column [string]
     */
    const QR_CODE = 'qr_code';

    /**
     *  Table column [string] serialized array
     */
    const CARD_DATA = 'card_data';

    /**
     *  Table column [string]
     */
    const VALUE_REMAINS = 'value_remains';

    /**
     * Table column [string]
     */
    const BASE_VALUE_REMAINS = 'base_value_remains';

    /**
     *  Table column [timestamp]
     */
    const CREATED_AT = 'created_at';

    /**
     *  Table column [timestamp]
     */
    const UPDATED_AT = 'updated_at';

    /**
     *  Table column [timestamp]
     */
    const VALID_UNTIL = 'valid_until';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return string
     */
    public function getQrCode();

    /**
     * @return string
     */
    public function getCardData();

    /**
     * @return string
     */
    public function getValueRemains();

    /**
     * @return string
     */
    public function getBaseValueRemains();

    /**
     * @return $this
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @return string
     */
    public function getValidUntil();

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @param string $qrCode
     *
     * @return $this
     */
    public function setQrCode($qrCode);

    /**
     * @param string $cardData
     *
     * @return $this
     */
    public function setCardData($cardData);

    /**
     * @param string $valueRemains
     *
     * @return $this
     */
    public function setValueRemains($valueRemains);

    /**
     * @param string $baseValueRemains
     *
     * @return $this
     */
    public function setBaseValueRemains($baseValueRemains);

    /**
     * @param Timestamp
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @param Timestamp
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @param Timestamp
     *
     * @return $this
     */
    public function setValidUntil($validUntil);
}