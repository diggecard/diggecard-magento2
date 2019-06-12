<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Diggecard\Giftcard\Api\Data\GiftcardInterface;

/**
 * Class Giftcard
 *
 * @package Diggecard\Giftcard\Model\ResourceModel
 */
class Giftcard extends AbstractDb
{
    /**
     * Main table name
     */
    const TABLE_NAME = 'diggecard_giftcard';

    /**
     * Source resource model constructor
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, GiftcardInterface::ENTITY_ID);
    }
}