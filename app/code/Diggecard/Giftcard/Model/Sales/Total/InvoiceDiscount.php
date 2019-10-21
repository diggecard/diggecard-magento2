<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model\Sales\Total;

use Diggecard\Giftcard\Model\Product\Price;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Sales\Model\Order\Invoice as ModelInvoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magento\Quote\Api\CartRepositoryInterface;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Diggecard\Giftcard\Service\CurrencyConverter;

/**
 * Class InvoiceDiscount
 *
 * @package Diggecard\Giftcard\Model\Sales\Total
 */
class InvoiceDiscount extends AbstractTotal
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var CurrencyConverter
     */
    private $currencyConverter;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param CartRepositoryInterface $cartRepository
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param CurrencyConverter $currencyConverter
     * @param array $data
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CartRepositoryInterface $cartRepository,
        GiftcardRepositoryInterface $giftcardRepository,
        CurrencyConverter $currencyConverter,
        $data = []
    ) {
        parent::__construct($data);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->cartRepository = $cartRepository;
        $this->giftcardRepository = $giftcardRepository;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ModelInvoice $invoice)
    {
        parent::collect($invoice);
        $order = $invoice->getOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->cartRepository->get($quoteId);
        $invoiceGrandTotal = $invoice->getGrandTotal();
        $baseGrandInvoiceTotal = $invoice->getBaseGrandTotal();

        if ($quote->getDiggecardGiftcardDiscount()){

            $label = 'Diggerecard Giftcard';

            $giftcardDiscountValue = -$quote->getDiggecardGiftcardDiscount();
            $baseGiftcardDiscountValue = $this->currencyConverter->convertToBaseCurrency($giftcardDiscountValue);

            $totalGiftcardDiscountInvoiced = 0;
            $baseTotalGiftcardDiscountInvoiced = 0;

            if ($order->getDgGiftcardAmountInvoiced()) {
                $totalGiftcardDiscountInvoiced = $order->getDgGiftcardAmountInvoiced();
                $baseTotalGiftcardDiscountInvoiced = $this->currencyConverter->convertToBaseCurrency($totalGiftcardDiscountInvoiced);
            }

            if ($invoice->isLast()) {
                $prevLabels = '';
                foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
                    if ($previousInvoice->getDiggecardGiftcardDiscount()) {
                        $totalGiftcardDiscountInvoiced += $previousInvoice->getDiggecardGiftcardDiscount();
                        $baseTotalGiftcardDiscountInvoiced = $this->currencyConverter->convertToBaseCurrency($totalGiftcardDiscountInvoiced);

                        if($totalDiscountDescription = $invoice->getDiscountDescription()) {
                            $prevLabels = $totalDiscountDescription.', ';
                        }
                    }
                }

                $label = $prevLabels.$label;
            }

            $notInvoicedGiftcardValue = $giftcardDiscountValue - $totalGiftcardDiscountInvoiced; //check for discount value to use
            $baseNotInvoicedGiftcardValue = $baseGiftcardDiscountValue - $baseTotalGiftcardDiscountInvoiced;

            if ($notInvoicedGiftcardValue > 0) { //if it exist (greater then zero)
                if ($notInvoicedGiftcardValue >= $invoiceGrandTotal) {
                    $totalDiscountAmountGiftcard = $invoiceGrandTotal;
                    $baseTotalDiscountAmountGiftcard = $baseGrandInvoiceTotal;
                } else {
                    $totalDiscountAmountGiftcard = $notInvoicedGiftcardValue;
                    $baseTotalDiscountAmountGiftcard = $baseNotInvoicedGiftcardValue;
                }
            }

            $quoteGiftcardId = $quote->getDiggecardGiftcardId();
            $quoteGiftcard = $this->giftcardRepository->get($quoteGiftcardId);
            $quoteGiftcard->setValueRemains($totalDiscountAmountGiftcard);

            $order->setDgGiftcardAmountInvoiced($invoiceGrandTotal);
            $order->setDgGiftcardBaseAmountInvoiced($baseGrandInvoiceTotal);

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmountGiftcard);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmountGiftcard);

            $invoice->setDiscountAmount($totalDiscountAmountGiftcard);
            $invoice->setBaseDiscountAmount($baseTotalDiscountAmountGiftcard);

            $invoice->setDiscountDescription($label);
        }
    }
}