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
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class CaptureValue
 *
 * @package Diggecard\Giftcard\Observer\Sales
 */
class CaptureValue implements ObserverInterface
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
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var Log
     */
    private $logger;

    /**
     * ReserveValue constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param GiftcardApiRepositoryInterface $giftcardApiRepository
     * @param ManagerInterface $messageManager
     * @param Log $logger
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        GiftcardRepositoryInterface $giftcardRepository,
        GiftcardApiRepositoryInterface $giftcardApiRepository,
        ManagerInterface $messageManager,
        Log $logger
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardApiRepository = $giftcardApiRepository;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
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
         * @var Invoice $invoice
         */
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getId() && $invoice->getState() == Invoice::STATE_PAID) {
            $this->logger->saveLog(__('Capture observer'));
            $salesOrder = $invoice->getOrder();
            $giftcardId = $salesOrder->getDiggecardGiftcardId();
            $reserveId = $salesOrder->getDiggecardGiftcardReservationId();

            if ($giftcardId && $reserveId) {
                $giftcard = $this->giftcardRepository->get($giftcardId);
                $orderDiscount = $salesOrder->getDiggecardGiftcardDiscount();
                $orderBaseDiscount = $salesOrder->getDiggecardGiftcardBaseDiscount();

                $data = [
                    "reservationCode" => $reserveId,  // reserve id
                    "merchantId" => "",
                    "qrCode" => (string)$giftcard->getQrCode(),
                    "amount" => number_format(abs($orderBaseDiscount), 2, '.', ''),
                    "totalOrderAmount" => (float)$invoice->getSubtotal()
                ];
                $this->logger->saveLog(__('Capture value'));
                $this->logger->saveLog($data);
                $result = $this->giftcardApiRepository->postCaptureReservedGiftcardAmount($data);
                $this->logger->saveLog($result);
                if (isset($result['validationErrors'])) {
                    $errors = $result['validationErrors'];
                    $this->messageManager->addErrorMessage(__('Cannot capture funds from giftcard!'));
                    throw new LocalizedException(
                        __(
                            "Cannot use gift card right now"
                        )
                    );
                }
                $giftcard->setCardData(json_encode($result));
                $this->giftcardRepository->save($giftcard);
            }
        }
    }
}
