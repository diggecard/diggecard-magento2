<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */
namespace Diggecard\Giftcard\Api\Data;
/**
 * Interface GiftcardSearchResultsInterface
 *
 * @package Diggecard\Giftcard\Api\Data
 */
interface GiftcardSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get list.
     *
     * @return \Diggecard\Giftcard\Api\Data\GiftcardInterface[]
     */
    public function getItems();
    /**
     * Set list.
     *
     * @param \Diggecard\Giftcard\Api\Data\GiftcardInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}