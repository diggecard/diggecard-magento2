<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Controller\Cart;

use Diggecard\Giftcard\Service\GiftcardSampleData as GiftcardService;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Diggecard\Giftcard\Service\ImportImageService;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Exception;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ProductOptionFactory;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Checkout\Model\Session;

/**
 * Class Add
 *
 * @package Diggecard\Giftcard\Controller\Cart
 */
class Add extends Action
{

    const DG_SKU = 'dg-general-giftcard';

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Product
     */
    protected $productModel;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var GiftcardService
     */
    protected $giftcardService;

    /**
     * @var ImportImageService
     */
    protected $imageService;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var ProductCustomOptionInterfaceFactory
     */
    protected $customOptionsFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ProductOptionFactory
     */
    protected $productOptionFactory;

    /**
     * @var CartItemInterfaceFactory
     */
    protected $cartItemFactory;

    /**
     * @var CartItemRepositoryInterface
     */
    protected $cartItemRepository;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Add constructor.
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Product $productModel
     * @param CartRepositoryInterface $quoteRepository
     * @param JsonFactory $resultJsonFactory
     * @param GiftcardService $giftcardService
     * @param ImportImageService $imageService
     * @param FormKey $formKey
     * @param ProductCustomOptionInterfaceFactory $customOptionsFactory
     * @param LoggerInterface $logger
     * @param ProductOptionFactory $productOptionFactory
     * @param CartItemInterfaceFactory $cartItemFactory
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Product $productModel,
        CartRepositoryInterface $quoteRepository,
        JsonFactory $resultJsonFactory,
        GiftcardService $giftcardService,
        ImportImageService $imageService,
        FormKey $formKey,
        ProductCustomOptionInterfaceFactory $customOptionsFactory,
        LoggerInterface $logger,
        ProductOptionInterfaceFactory $productOptionFactory,
        CartItemInterfaceFactory $cartItemFactory,
        CartItemRepositoryInterface $cartItemRepository,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->productModel = $productModel;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->giftcardService = $giftcardService;
        $this->imageService = $imageService;
        $this->formKey = $formKey;
        $this->customOptionsFactory = $customOptionsFactory;
        $this->logger = $logger;
        $this->productOptionFactory = $productOptionFactory;
        $this->cartItemFactory = $cartItemFactory;
        $this->cartItemRepository = $cartItemRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();

        $formKey = $this->formKey->getFormKey();
        $product = $this->productRepository->get(self::DG_SKU);
        $options = [];
        $optionData = $this->createOptionData($data);
        $productOption = $this->productOptionFactory->create();
        $extAttribute = $productOption->getExtensionAttributes();
        $extAttribute->setCustomOptions($optionData);
        $productOption->setExtensionAttributes($extAttribute);

        $quote = $this->checkoutSession->getQuote();
        $qty = 1; // assign product quantity which you want to add
        $quoteId = $quote->getEntityId(); // here give quote id

        $cartItem = $this->cartItemFactory->create();  // cartItem is an instance of Magento\Quote\Api\Data\CartItemInterface

        // set product sku to cart item
        $cartItem->setSku($product->getSku());

        // assign quote Id to cart item
        $cartItem->setQuoteId($quoteId);

        // set product Quantity
        $cartItem->setQty($qty);

        $price = $data['price'];
        $cartItem->setPrice($price);

        $cartItem->setCustomPrice($price);
        $cartItem->setOriginalCustomPrice($price);
        $cartItem->getProduct()->setIsSuperMode(true);

        // set product options to cart item
        $cartItem->setProductOption($productOption);

        // add ptoduct to cart
        $newItem = $this->cartItemRepository->save($cartItem);

        $response = [
            'valid' => true,
            'message' => 'Giftcard Created!',
            'url' => 'checkout/cart'
        ];

        return $this->resultJsonFactory->create()->setData($response);
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function createOptionData($data) {
        $optionData = [];

        try {
            $imageImportResponse = $this->imageService->importImage($data['image']);

        }catch (Exception $exception) {
            $this->logger->critical(__('Create Option Data'), ['exception' => $exception]);
            return false;
        }
        $imageUrl = $imageImportResponse['newFileName'];
        $hashCode = $data['hash'];
        $customPrice = $data['value'];

        $options = [
            [
                "option_id" => "dg_giftcard_image",
                "option_value" => $imageUrl
            ],
            [
                "option_id" => "dg_giftcard_hash_code",
                "option_value" => $hashCode
            ],
            [
                "option_id" => "dg_giftcard_custom_price",
                "option_value" => $customPrice
            ]
        ];
        $customOptionInterface = $this->customOptionsFactory->create();
        foreach ($options as $option) {
            $optionData[] = $customOptionInterface->setData($option);
        }


        return $optionData;
    }
}