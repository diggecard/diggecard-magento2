<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Model\Sales\Total;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Sales\Model\Order\Invoice as ModelInvoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Diggecard\Giftcard\Api\GiftcardRepositoryInterface;
use Diggecard\Giftcard\Service\CurrencyConverter;
use Diggecard\Giftcard\Model\Config;

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
     * @var Config
     */
    protected $config;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param CurrencyConverter $currencyConverter
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        GiftcardRepositoryInterface $giftcardRepository,
        CurrencyConverter $currencyConverter,
        Config $config,
        $data = []
    )
    {
        parent::__construct($data);
        $this->dataObjectHelper = $dataObjectHelper;
        $this->giftcardRepository = $giftcardRepository;
        $this->currencyConverter = $currencyConverter;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ModelInvoice $invoice)
    {
        parent::collect($invoice);
        $order = $invoice->getOrder();
        $invoiceGrandTotal = $invoice->getGrandTotal();
        $baseGrandInvoiceTotal = $invoice->getBaseGrandTotal();

        if ($order->getDiggecardGiftcardDiscount()) {

            $label = $this->config->getDiscountLabel();

            $giftcardDiscountValue = -$order->getDiggecardGiftcardDiscount();
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

                        if ($totalDiscountDescription = $invoice->getDiscountDescription()) {
                            $prevLabels = $totalDiscountDescription . ', ';
                        }
                    }
                }

                $label = $prevLabels . $label;
            }

            $notInvoicedGiftcardValue = $giftcardDiscountValue - $totalGiftcardDiscountInvoiced;
            $baseNotInvoicedGiftcardValue = $baseGiftcardDiscountValue - $baseTotalGiftcardDiscountInvoiced;

            if ($notInvoicedGiftcardValue > 0) {
                if ($notInvoicedGiftcardValue >= $invoiceGrandTotal) {
                    $totalDiscountAmountGiftcard = $invoiceGrandTotal;
                    $baseTotalDiscountAmountGiftcard = $baseGrandInvoiceTotal;
                } else {
                    $totalDiscountAmountGiftcard = $notInvoicedGiftcardValue;
                    $baseTotalDiscountAmountGiftcard = $baseNotInvoicedGiftcardValue;
                }
            }

            $orderGiftcardId = $order->getDiggecardGiftcardId();
            $orderGiftcard = $this->giftcardRepository->get($orderGiftcardId);
            $orderGiftcard->setValueRemains($totalDiscountAmountGiftcard);

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
