<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class GiftcardConfigProvider
 *
 * @package Diggecard\Giftcard\Model
 */
class GiftcardConfigProvider implements ConfigProviderInterface
{
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


    public function __construct(
        CheckoutSession $checkoutSession,
        GiftcardRepositoryInterface $giftcardRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->giftcardRepository = $giftcardRepository;
        $this->_storeManager = $storeManager;
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

        return $config;
    }
}