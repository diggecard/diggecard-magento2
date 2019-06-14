<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product;
use Diggecard\Giftcard\Model\Product\Type\Giftcard as GiftcardType;
use Diggecard\Giftcard\Service\GiftcardSampleData;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Class InstallData
 *
 * @package Diggecard\Giftcard\SetupquoteSetup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var GiftcardSampleData
     */
    private $giftcardSampleData;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param GiftcardSampleData $giftcardSampleData
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        GiftcardSampleData $giftcardSampleData,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory

    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->giftcardSampleData = $giftcardSampleData;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->giftcardSampleData->addGiftcardProduct();
    }
}
