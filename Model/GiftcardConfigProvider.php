<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class GiftcardConfigProvider
 *
 * @package Diggecard\Giftcard\Model
 */
class GiftcardConfigProvider implements ConfigProviderInterface
{
    const XML_MODULE_STATUS_PATH = 'diggecard/giftcard/active';

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var GiftcardRepositoryInterface
     */
    protected $giftcardRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /** @var ScopeConfigInterface */
    protected $_scopeConfig;


    public function __construct(
        CheckoutSession $checkoutSession,
        GiftcardRepositoryInterface $giftcardRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->giftcardRepository = $giftcardRepository;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $giftcardId = $this->checkoutSession->getQuote()->getData('diggecard_giftcard_id');
        $isDiscountApplied = $giftcardId !== null ? true : false;
        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $config = [
            'diggecard' => [
                'giftcard' => [
                    'isDiscountApplied' => $isDiscountApplied,
                ]
            ]
        ];

        if ($isDiscountApplied && $giftcard = $this->giftcardRepository->get($giftcardId)) {
            $config['diggecard']['giftcard']['qrCode'] = $giftcard->getQrCode();
            $config['diggecard']['giftcard']['valueRemains'] = $giftcard->getValueRemains();
            $config['diggecard']['giftcard']['validUntil'] = $giftcard->getValidUntil();
            $config['diggecard']['giftcard']['currencyCode'] = $currentCurrencyCode;
        }

        $config['diggecard']['isEnable'] = $this->isModuleEnable();

        return $config;
    }

    public function isModuleEnable()
    {
        return (bool)$this->_scopeConfig->getValue(
            self::XML_MODULE_STATUS_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }
}