<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Controller\Giftcard;

use Diggecard\Giftcard\Helper\Log;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Diggecard\Giftcard\Service\GiftcardSampleData as GiftcardService;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class addToCheckout
 *
 * @package Diggecard\Giftcard\Controller\Giftcard\Index
 */
class Add extends Action
{
    const DG_SKU = 'dg-general-giftcard';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var GiftcardService
     */
    protected $giftcardService;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var
     */
    private $logger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Index constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param GiftcardService $giftcardService
     * @param ProductRepositoryInterface $productRepository
     * @param Log $logger
     * @param FormKey $formKey
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        GiftcardService $giftcardService,
        ProductRepositoryInterface $productRepository,
        Log $logger,
        FormKey $formKey,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->giftcardService = $giftcardService;
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context);
    }


    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $post = $this->getRequest()->getParams();
            $quote = $this->checkoutSession->getQuote();
            if (!$quote->getDiggecardGiftcardId()) {


                /** @var Product $product */
                try {
                    $product = $this->productRepository->get(self::DG_SKU);
                    $formKey = $this->formKey->getFormKey();

                    $params = array(
                        'form_key' => $formKey,
                        'product' => $product->getEntityId(),
                        'qty' => 1,
                        'price' => $post['value'],
                        'dg_giftcard_image' => $post['image'],
                        'dg_giftcard_value' => $post['value'],
                        'dg_giftcard_hash' => $post['hash']
                    );
                    if ($quote->getItemsCount() > 0) {
                        $cartItems = $quote->getAllItems();
                        foreach ($cartItems as $cartItem) {
                            $itemOption = $cartItem->getOptionByCode('dg_giftcard_hash');
                            if ($itemOption && $itemOption->getValue() == $params['dg_giftcard_hash']) {
                                if ($cartItem->getOptionByCode('dg_giftcard_value')->getValue() !== $params['dg_giftcard_value']) {
                                    $quote->removeItem($cartItem->getItemId());
                                    $this->messageManager->addNoticeMessage(__('Card value updated'));
                                    break;
                                } else {
                                    $this->messageManager->addNoticeMessage(__('Card was already added to cart'));
                                    return $this->resultJsonFactory->create()->setData([$params, $itemOption]);
                                }
                            }
                        }
                    }
                    $this->_forward('add', 'cart', "checkout", $params);
                } catch (NoSuchEntityException $e) {
                    $this->logger->saveLog(__FILE__ . ' cannot find GC product', Log::TYPE_EXCEPTION);
                    $this->messageManager->addErrorMessage(__('Cannot add GiftCard to cart'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('Cannot purchase giftcard via giftcard!'));
            }
        }
    }
}