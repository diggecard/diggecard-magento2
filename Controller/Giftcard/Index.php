<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Controller\Giftcard;

use Diggecard\Giftcard\Model\GiftcardConfigProvider;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Diggecard\Giftcard\Controller\Giftcard\Index
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /** @var GiftcardConfigProvider */
    protected $_giftcardConfigProvider;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        GiftcardConfigProvider $giftcardConfigProvider
    )
    {
        $this->pageFactory = $pageFactory;
        $this->_giftcardConfigProvider = $giftcardConfigProvider;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $page = $this->pageFactory->create();

        if (!$this->_giftcardConfigProvider->isModuleEnable()) {
            throw new NotFoundException(__('404 Page not found.'));
        }

        $page->getConfig()->getTitle()->set(__('Buy GiftCard'));
        return $page;
    }
}