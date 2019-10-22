<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Observer\Sales;

use Diggecard\Giftcard\Api\GiftcardApiRepositoryInterface;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Diggecard\Giftcard\Helper\Log;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Diggecard\Giftcard\Model\Config;

/**
 * Class ReserveValue
 *
 * @package Diggecard\Giftcard\Observer\Sales
 */
class ReserveValue implements ObserverInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var GiftcardRepositoryInterface
     */
    protected $giftcardRepository;

    /**
     * @var GiftcardApiRepositoryInterface
     */
    protected $giftcardApiRepository;
    /**
     * @var Log
     */
    private $logger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * ReserveValue constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param GiftcardApiRepositoryInterface $giftcardApiRepository
     * @param Log $logger
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        GiftcardRepositoryInterface $giftcardRepository,
        GiftcardApiRepositoryInterface $giftcardApiRepository,
        Log $logger,
        Config $config
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardApiRepository = $giftcardApiRepository;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /**
         * @var OrderInterface $salesOrder
         */
        $salesOrder = $observer->getEvent()->getOrder();
        if ($salesOrder->getQuoteId()) {

            $quoteId = $salesOrder->getQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            $giftcardId = $quote->getDiggecardGiftcardId();
            $timeToReserve = $this->config->getTimeToReserve();
            if ($giftcardId) {
                $giftcard = $this->giftcardRepository->get($giftcardId);
                $quoteDiscount = $quote->getDiggecardGiftcardDiscount();
                $quoteBaseDiscount = $quote->getDiggecardGiftcardBaseDiscount();
                $data = [
                    "minutesToReserve" => $timeToReserve,
                    "merchantId" => "",
                    "qrCode" => (string)$giftcard->getQrCode(),
                    "amount" => number_format(abs($quoteBaseDiscount), 2, '.', '')
                ];
                $this->logger->saveLog(__('Reserve value'));
                $this->logger->saveLog($data);
                $result = $this->giftcardApiRepository->postReserveGiftcardAmount($data);
                $this->logger->saveLog(__('RESULT:'));
                $this->logger->saveLog($result);
                if (isset($result['validationErrors'])) {
                    $errors = $result['validationErrors'];
                    $message = isset($errors['amount']) ? $errors['amount'] : "Cannot use gift card right now";
                    throw new LocalizedException(
                        __(
                            $message
                        )
                    );
                }
                $this->logger->saveLog($result);
                $quote->setDiggecardGiftcardReservationId($result['reservationCode']);
                $giftcard->setCardData(json_encode($result));
                $giftcard->setValueRemains($giftcard->getValueRemains() - abs($quoteBaseDiscount));
                $this->giftcardRepository->save($giftcard);
            }
        }
    }
}
