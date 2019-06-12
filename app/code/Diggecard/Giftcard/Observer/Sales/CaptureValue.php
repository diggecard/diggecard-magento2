<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Observer\Sales;

use Diggecard\Giftcard\Api\GiftcardApiRepositoryInterface;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Diggecard\Giftcard\Helper\Log;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
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
     * @var \Magento\Framework\Message\ManagerInterface
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
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Log $logger
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        GiftcardRepositoryInterface $giftcardRepository,
        GiftcardApiRepositoryInterface $giftcardApiRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order\Invoice $invoice
         */
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getId() && $invoice->getState() == Invoice::STATE_PAID) {
            $this->logger->saveLog('Capture observer');
            $salesOrder = $invoice->getOrder();
            $quoteId = $salesOrder->getQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            $giftcardId = $quote->getDiggecardGiftcardId();
            $reserveId = $quote->getDiggecardGiftcardReservationId();
            if ($giftcardId && $reserveId) {
                $giftcard = $this->giftcardRepository->get($giftcardId);
                $quoteDiscount = $quote->getDiggecardGiftcardDiscount();
                $quoteBaseDiscount = $quote->getDiggecardGiftcardBaseDiscount();
                $data = [
                    "reservationCode" => $reserveId,  // reserve id
                    "merchantId" => "",
                    "qrCode" => (string)$giftcard->getQrCode(),
                    "amount" => number_format(abs($quoteBaseDiscount), 2, '.', '')
                ];
                $this->logger->saveLog('Capture value');
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