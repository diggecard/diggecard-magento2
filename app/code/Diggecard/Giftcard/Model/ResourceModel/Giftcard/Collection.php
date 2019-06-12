<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Model\ResourceModel\Giftcard;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Diggecard\Giftcard\Api\Data\GiftcardInterface;

/**
 * Class Collection
 *
 * @package Diggecard\Giftcard\Model\ResourceModel\Giftcard
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = GiftcardInterface::ENTITY_ID;

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Diggecard\Giftcard\Model\Giftcard',
            'Diggecard\Giftcard\Model\ResourceModel\Giftcard'
        );
    }
}
