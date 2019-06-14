<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Controller\Checkout;

use Diggecard\Giftcard\Api\Data\GiftcardInterface;
use Diggecard\Giftcard\Helper\Log;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Diggecard\Giftcard\Model\Giftcard\Manager as GiftcardManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Apply
 *
 * @package Diggecard\Giftcard\Controller\Checkout
 */
class Apply extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var GiftcardManager
     */
    protected $giftcardManager;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Log
     */
    private $logger;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    const GIFTCARD_SKU = ['dg-general-giftcard'];

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CheckoutSession $checkoutSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftcardManager $giftcardManager
     * @param StoreManagerInterface $storeManager
     * @param Log $logger
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CheckoutSession $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        CartRepositoryInterface $quoteRepository,
        GiftcardManager $giftcardManager,
        StoreManagerInterface $storeManager,
        Log $logger,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->priceCurrency = $priceCurrency;
        $this->quoteRepository = $quoteRepository;
        $this->giftcardManager = $giftcardManager;
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
        $this->_messageManager = $messageManager;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $response = [
            'valid' => false
        ];

        if ($this->getRequest()->isAjax()) {
            $post = $this->getRequest()->getParams();
            $qrCode = $post['qrCode'];
            if (strlen($qrCode) == 0) {
                $message = 'Enter Gift Card Code!';
                $this->_messageManager->addErrorMessage(__($message));
                $response = [
                    'valid' => false,
                    'error_type' => 5,
                    'message' => 'Enter Gift Card Code!'
                ];
                return $result->setData($response);
            }
            $quote = $this->checkoutSession->getQuote();

            $this->logger->saveLog('GiftCard validation on cart');
            /** @var GiftcardInterface $giftcard */
            $giftcard = $this->giftcardManager->validateGiftcard($qrCode, true);
            if (!$giftcard) {
                $message = 'No such Gift Card with code: '.$qrCode;
                $this->_messageManager->addErrorMessage(__($message));
                $response = [
                    'valid' => false,
                    'error_type' => 4,
                    'message' => 'No such giftcard!'
                ];
                return $result->setData($response);
            }

            $this->logger->saveLog($giftcard->getData());

            foreach (self::GIFTCARD_SKU as $sku) {
                foreach ($quote->getAllItems() as $item) {
                    if ($sku == $item->getSku()) {
                        $response = [
                            'valid' => false,
                            'error_type' => 2,
                            'message' => 'Cannot purchase giftcard via giftcard'
                        ];
                        return $result->setData($response);
                    }
                }
            }

            if ($giftcard->getValueRemains() <= 0) {
                $response = [
                    'valid' => false,
                    'error_type' => 3,
                    'message' => 'Cannot apply empty giftcard'
                ];
                return $result->setData($response);
            }

            if ($giftcard && $giftcard->getEntityId()) {

                $giftcardEntityId = $giftcard->getEntityId();
                $quote->setData('diggecard_giftcard_id', $giftcardEntityId);

                $this->quoteRepository->save($quote);

                $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
                $response = [
                    'valid' => true,
                    'giftcardId' => $giftcard->getEntityId(),
                    'giftcardQrCode' => $giftcard->getQrCode(),
                    'giftcardValueRemains' => $giftcard->getValueRemains(),
                    'currentCurrency' => $currentCurrencyCode
                ];
            } else {
                $response = [
                    'valid' => false,
                    'error_type' => 1,
                    'message' => 'There is no gift card with such QR Code'
                ];
            }
        }
        return $result->setData($response);

    }
}