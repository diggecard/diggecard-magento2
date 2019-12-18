<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Model\Sales\Total;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Diggecard\Giftcard\Model\Config;

/**
 * Class QuoteDiscount
 *
 * @package Diggecard\Giftcard\Model\Total\Quote
 */
class QuoteDiscount extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var GiftcardRepositoryInterface
     */
    protected $giftcardRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * GiftcardTotal constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param Config $config
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        GiftcardRepositoryInterface $giftcardRepository,
        Config $config
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->giftcardRepository = $giftcardRepository;
        $this->config = $config;
        $this->setCode('diggecard_giftcard_discount');
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this|Total\AbstractTotal
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);

        $quoteGiftcardId = $quote->getData('diggecard_giftcard_id');
        if ($quoteGiftcardId && $giftCard = $this->giftcardRepository->get($quoteGiftcardId)) {

            $items = $shippingAssignment->getItems();
            if (!count($items)) {
                return $this;
            }

            $subtotal = (double)$total->getSubtotalInclTax() + $total->getDiscountAmount() + $total->getShippingTaxAmount();
            $subtotal += $total->getShippingAmount() ? $total->getShippingAmount() : 0;
            $discountAmount = ((double)$giftCard->getValueRemains() > $subtotal) ? $subtotal : (double)$giftCard->getValueRemains();
            $discountAmount = -$discountAmount;

            $quote->setDiggecardGiftcardDiscount($discountAmount);
            $quote->setDiggecardGiftcardBaseDiscount($discountAmount);

            $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
            $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);

            $total->setTotalAmount($this->getCode(), $discountAmount);
            $total->setBaseTotalAmount($this->getCode(), $discountAmount);

            $total->setGrandTotal($total->getGrandTotal() + $discountAmount);
            $total->setBaseGrandTotal($total->getBaseGrandTotal() + $discountAmount);
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => $quote->getDiggecardGiftcardDiscount()
        ];
    }

    /**
     * get label
     * @return string
     */
    public function getLabel() {
        return __($this->config->getDiscountLabel());
    }

}
