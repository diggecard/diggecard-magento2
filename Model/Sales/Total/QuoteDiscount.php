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

    const CODE = 'diggecard_giftcard_discount';

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

            $label = $this->config->getDiscountLabel();
            $subtotal = (double)$total->getSubtotalInclTax() + $total->getDiscountAmount() + $total->getShippingTaxAmount();
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
                $label = $total->getDiscountDescription() ? $total->getDiscountDescription() : $label;
            }

            $total->setDiscountDescription($label);
            $total->setDiscountAmount($discountAmount);
            $total->setBaseDiscountAmount($discountAmount);
        }

        return $this;
    }
}