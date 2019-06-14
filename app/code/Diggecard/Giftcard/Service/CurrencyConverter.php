<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Service;

use Magento\Quote\Model\Cart\Currency;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CurrencyConverter
 *
 * @package Diggecard\Giftcard\Service
 */
class CurrencyConverter
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @return Currency
     * @throws NoSuchEntityException
     */
    public function getBaseCurrency()
    {
        return $baseCurrency = $this->storeManager->getStore()->getBaseCurrency();
    }

    /**
     * @return Currency
     * @throws NoSuchEntityException
     */
    public function getCurrentCurrecny()
    {
        return $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency();
    }

    /**
     * @param float|int $price
     * @param string $codeFrom
     * @param string $codeTo
     * @return float|int
     */
    public function convertPrice($price, $codeFrom, $codeTo)
    {
        if($codeFrom != $codeTo) {
            $rate = $this->currencyFactory->create()->load($codeFrom)->getAnyRate($codeTo);
            $returnValue = $price * $rate;
            return $returnValue;
        }
        return $price;
    }

    /**
     * @param $price
     * @return float|int
     * @throws NoSuchEntityException
     */
    public function convertToBaseCurrency($price)
    {
        $codeFrom = $this->getCurrentCurrecny();
        $codeTo = $this->getBaseCurrency();

        if($codeFrom !== $codeTo) {
            $rate = $this->currencyFactory->create()->load($codeFrom)->getAnyRate($codeTo);
            $returnValue = $price * $rate;
            return $returnValue;
        }
        return $price;
    }
}