<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Controller\Giftcard;

use Diggecard\Giftcard\Helper\Log;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Diggecard\Giftcard\Service\GiftcardSampleData as GiftcardService;
use Diggecard\Giftcard\Service\ImportImageService as GiftcardImportImageService;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Class addToCheckout
 *
 * @package Diggecard\Giftcard\Controller\Giftcard\Index
 */
class Add extends Action
{
    const DG_SKU = 'dg-general-giftcard';
    const DEFAULT_RESIZE_DIRECTORY = 'resize';
    const DEFAULT_WIDTH = 300;
    const DEFAULT_HEIGHT = 300;

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
     * @var GiftcardImportImageService
     */
    private $giftcardImportImageService;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * Index constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param GiftcardService $giftcardService
     * @param ProductRepositoryInterface $productRepository
     * @param Log $logger $filesystem = $objectManager->create('\Magento\Framework\Filesystem');
     * @param FormKey $formKey
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param GiftcardImportImageService $giftcardImportImageService
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        GiftcardService $giftcardService,
        ProductRepositoryInterface $productRepository,
        Log $logger,
        FormKey $formKey,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        GiftcardImportImageService $giftcardImportImageService,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        StoreManagerInterface $storeManager
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->giftcardService = $giftcardService;
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->giftcardImportImageService = $giftcardImportImageService;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
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

                    $importImage = $this->giftcardImportImageService->importImage($params['dg_giftcard_image']);
                    $width = self::DEFAULT_WIDTH;
                    $height = self::DEFAULT_HEIGHT;
                    $imgResizePath = $this->imageResize(baseName($importImage['newFileName']), $width, $height, self::DEFAULT_RESIZE_DIRECTORY);
                    $params['dg_giftcard_image'] = $imgResizePath;

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

    private function imageResize(
        $src,
        $width,
        $height,
        $dir = self::DEFAULT_RESIZE_DIRECTORY.DIRECTORY_SEPARATOR
    )
    {
        $resizedURL = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
            $dir . $this->getNewDirectoryImage($src);

        $absPath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . 'tmp' . DIRECTORY_SEPARATOR . $src;

        $imageResized = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($dir) .
            $this->getNewDirectoryImage($src);

        if (file_exists($imageResized)) {
            return $resizedURL;
        }

        /** @var AdapterInterface $imageResize */
        $imageResize = $this->imageFactory->create();
        $imageResize->open($absPath);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(true);
        $imageResize->quality('100');
        $imageResize->resize($width, $height);
        $dest = $imageResized;
        $imageResize->save($dest);
        return $resizedURL;
    }

    private function getNewDirectoryImage($src)
    {
        $segments = array_reverse(explode('/', $src));
        $first_dir = substr($segments[0], 0, 1);
        $second_dir = substr($segments[0], 1, 1);
        return 'cache'. DIRECTORY_SEPARATOR . $first_dir . DIRECTORY_SEPARATOR . $second_dir . DIRECTORY_SEPARATOR . $segments[0];
    }

}