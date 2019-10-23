<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Controller\Checkout;

use Diggecard\Giftcard\Api\Data\GiftcardInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Diggecard\Giftcard\Model\Giftcard\Manager as GiftcardManager;

/**
 * Class Cancel
 *
 * @package Diggecard\Giftcard\Controller\Index
 */
class Cancel extends \Magento\Framework\App\Action\Action
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
     * Index constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CheckoutSession $checkoutSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftcardManager $giftcardManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CheckoutSession $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        CartRepositoryInterface $quoteRepository,
        GiftcardManager $giftcardManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->priceCurrency = $priceCurrency;
        $this->quoteRepository = $quoteRepository;
        $this->giftcardManager = $giftcardManager;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if ($this->getRequest()->isAjax()) {
            $post = $this->getRequest()->getParams();
            $qrCode = $post['qrCode'];
            $quote = $this->checkoutSession->getQuote();

            /** @var GiftcardInterface $giftcard */
            $giftcard = $this->giftcardManager->validateGiftcard($qrCode);

            if($giftcardEntityId = $giftcard->getEntityId()) {
                $quote->setData('diggecard_giftcard_id', null);
            }

            $this->quoteRepository->save($quote);
            $quote->collectTotals();

            $response = [
                'giftcardId' => $giftcard->getEntityId(),
                'giftcardQrCode' => $giftcard->getQrCode()
            ];

            return $result->setData($response);
        }
    }
}