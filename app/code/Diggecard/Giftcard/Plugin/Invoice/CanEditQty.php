<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Plugin\Invoice;

use Exception;
use Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class CanEditQty
 *
 * @package Diggecard\Giftcard\Plugin\Invoice
 */
class CanEditQty
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * Items constructor.
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param Items $subject
     * @param $result
     * @return bool
     */
    public function afterCanEditQty(
        Items $subject,
        $result
    ) {
        $quoteId = $subject->getOrder()->getQuoteId();
        try {
            $quote = $this->cartRepository->get($quoteId);
            if ($quote->getDiggecardGiftcardId()) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $exception) {
            return false;
        }
    }
}
