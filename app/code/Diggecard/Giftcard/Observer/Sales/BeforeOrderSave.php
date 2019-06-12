<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */
namespace Diggecard\Giftcard\Observer\Api;

use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Diggecard\Giftcard\Model\Product\Type\Giftcard as GiftcardType;
use Magento\Customer\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BeforeOrderSave
 *
 * @package Diggecard\Giftcard\Observer\Api
 */
class BeforeOrderSave implements ObserverInterface
{

    /**
     * @var Order
     */
    protected $orderModel;

    /**
     * @var Session
     */
    protected $sessionManager;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var GiftcardRepositoryInterface
     */
    protected $giftcardRepository;

    /**
     * BeforeOrderSave constructor.
     * @param Order $orderModel
     * @param Session $sessionMeneger
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftcardRepositoryInterface $giftcardRepository
     */
    public function __construct(
        Order $orderModel,
        Session $sessionMeneger,
        CartRepositoryInterface  $quoteRepository,
        GiftcardRepositoryInterface $giftcardRepository
    ) {
        $this->orderModel = $orderModel;
        $this->sessionManager = $sessionMeneger;
        $this->quoteRepository = $quoteRepository;
        $this->giftcardRepository = $giftcardRepository;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @return OrderInterface
     */
    public function execute(Observer $observer) {
        /** @var OrderInterface */
        $order = $observer->getEvent()->getOrder();
        if (!$order->getEntityId()){
            $quoteId = $order->getQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            $giftcardId = $quote->getDiggecardGiftcardId();
            $giftcard = $this->giftcardRepository->get($giftcardId);
            $quoteDiscount = $quote->getDiggecardGiftcardDiscount();
            $quoteBaseDiscount = $quote->getDiggecardGiftcardBaseDiscount();
            $giftcard->setValueRemains($giftcard->getValueRemains() - abs($quoteDiscount));
        }

        return $order;
    }
}