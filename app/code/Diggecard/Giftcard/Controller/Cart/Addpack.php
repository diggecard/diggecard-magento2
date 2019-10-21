<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Action;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session;
use Exception;

/**
 * Class Addpack
 *
 * @package Diggecard\Giftcard\Controller\Cart
 */
class Addpack extends Action
{

    /**
     *  Product Giftcard SKU
     */
    const DG_SKU = 'dg-general-giftcard';

    /**
     * @var CartInterface
     */
    protected $cart;
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepositoryInterface;

    /**
     * @var Session
     */
    protected $checkoutSession;
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * Addpack constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $productFactory
     * @param CartInterface $cart
     * @param ProductRepositoryInterface $productRepository
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductFactory $productFactory,
        CartInterface $cart,
        ProductRepositoryInterface $productRepository,
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $postedData = $this->getRequest()->getPost();
                    $params = [
                      'hash' => $postedData['hash'],
                      'image' =>$postedData['image']
                    ];
                    $params['qty'] = 1;
                    $product = $this->productRepository->get(self::DG_SKU);
                    $cart = $this->checkoutSession->getQuote();

                    if ($product) {
                        $cart->addProduct($product, $params);
                    }

            $this->cartRepository->save($cart);

            $this->messageManager->addSuccessMessage(__('Add to cart successfully.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('%1', $e->getMessage())
            );
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('error.'));
        }

        $this->getResponse()->setRedirect('/checkout/cart/index');
    }
}