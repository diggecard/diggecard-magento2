<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Observer\Api\AfterOrderSave\Post\Giftcard;

use Diggecard\Giftcard\Api\Data\GiftcardInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Diggecard\Giftcard\Api\GiftcardApiRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Quote\Api\CartRepositoryInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Diggecard\Giftcard\Model\Giftcard\Manager as GiftcardManager;

/**
 * Class Capture
 *
 * @package Diggecard\Giftcard\Observer\Api
 */
class Capture implements ObserverInterface
{
    /**
     * @var GiftcardApiRepositoryInterface
     */
    protected $giftcardApiRepository;

    /**
     * @var GiftcardManager
     */
    protected $giftcardManager;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Capture constructor.
     * @param GiftcardApiRepositoryInterface $giftcardApiRepository
     * @param GiftcardManager $giftcardManager
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        GiftcardApiRepositoryInterface $giftcardApiRepository,
        GiftcardManager $giftcardManager,
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    )
    {
        $this->giftcardApiRepository = $giftcardApiRepository;
        $this->giftcardManager = $giftcardManager;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return OrderInterface
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $orderState = $order->getState();

        if ($orderState == 'new') {
            try {
                $quote = $this->quoteRepository->get($order->getQuoteId());

                if ($giftcardId = $quote->getDiggecardGiftcardId()) {
                    /** @var GiftcardInterface $giftcard */
                    $giftcard = $this->giftcardManager->giftcardRepository->get($giftcardId);

                    $data = [
                        "merchantId" => "",
                        "qrCode" => $giftcard->getQrCode(),
                        "amount" => $quote->getDiggecardGiftcardBaseDiscount(),
                        "reservationCode" => $quote->getDiggecardGiftcardReservationId(),
                        "totalOrderAmount" => (float)$quote->getSubtotal()
                    ];

                    $this->giftcardApiRepository->postCaptureReservedGiftcardAmount($data);
                }
            } catch (Exception $exception) {
                $this->logger->error('Diggecard Giftcard :: Cannot get quote after order save.');
                return $order;
            }
        }

        return $order;
    }
}