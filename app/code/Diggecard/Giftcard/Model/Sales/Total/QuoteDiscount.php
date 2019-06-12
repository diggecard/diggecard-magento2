<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Model\Sales\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

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

    const CODE = 'diggecard_giftcard_discount';

    /**
     * GiftcardTotal constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param GiftcardRepositoryInterface $giftcardRepository
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        GiftcardRepositoryInterface $giftcardRepository
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->giftcardRepository = $giftcardRepository;

    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this|Total\AbstractTotal
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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

            $label = 'Diggerecard Giftcard';
            $subtotal = (double)$total->getSubtotalInclTax() + $total->getDiscountAmount();
            $subtotal += $total->getShippingAmount() ? $total->getShippingAmount() : 0;
            $discountAmount = ((double)$giftCard->getValueRemains() > $subtotal) ? $subtotal : (double)$giftCard->getValueRemains();
            $discountAmount = -$discountAmount;


            $quote->setDiggecardGiftcardDiscount($discountAmount);
            $quote->setDiggecardGiftcardBaseDiscount($discountAmount);

            $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
            $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);

            $total->addTotalAmount($this->getCode(), $discountAmount);
            $total->addBaseTotalAmount($this->getCode(), $discountAmount);

            if ($total->getDiscountAmount()) {
                // If a discount exists in cart and another discount is applied, the add both discounts.
                $discountAmount = $total->getDiscountAmount() + $discountAmount;
                $label = $total->getDiscountDescription() ? $total->getDiscountDescription() : 'Store Discount, ' . $label;
            }

            $total->setDiscountDescription($label);
            $total->setDiscountAmount($discountAmount);
            $total->setBaseDiscountAmount($discountAmount);
        }


        return $this;
    }
}