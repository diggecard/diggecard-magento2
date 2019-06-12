<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Diggecard\Giftcard\Api\Data\GiftcardInterface;
use Diggecard\Giftcard\Model\ResourceModel\Giftcard as GiftcardResourceModel;
use Diggecard\Giftcard\Api\Data\DiscountInterface;
use Diggecard\Giftcard\Model\ResourceModel\Discount as DiscountResourceModel;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 *
 * @package Diggecard\Giftcard\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @param EavSetup $eavSetup
     * @param QuoteSetupFactory $setupFactory
     */
    public function __construct(
        EavSetup $eavSetup
    ) {
        $this->eavSetup = $eavSetup;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'Giftcard'
         */
        $table = $installer->getConnection()->newTable(
                $installer->getTable(GiftcardResourceModel::TABLE_NAME)
            )->addColumn(
                GiftcardInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Giftcard Entity Id'
            )->addColumn(
                GiftcardInterface::QR_CODE,
                Table::TYPE_TEXT,
                36,
                ['nullable' => true],
                'Giftcard Qr Code'
            )->addColumn(
                GiftcardInterface::CARD_DATA,
                Table::TYPE_TEXT,
                1024,
                ['nullable' => false],
                'Giftcard Data'
            )->addColumn(
                GiftcardInterface::VALUE_REMAINS,
                Table::TYPE_TEXT,
                36,
                ['nullable' => true],
                'Giftcard Value Remains'
            )->addColumn(
                GiftcardInterface::BASE_VALUE_REMAINS,
                Table::TYPE_TEXT,
                36,
                ['nullable' => true],
                'Giftcard Base Value Remains'
            )->addColumn(
                GiftcardInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                50,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Giftcard Created At'
            )->addColumn(
                GiftcardInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                50,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Giftcard Updated At'
            )->addColumn(
                GiftcardInterface::VALID_UNTIL,
                Table::TYPE_TIMESTAMP,
                50,
                ['nullable' => true],
                'Giftcard Valid Until'
            )->setComment('DiggEcard GiftCard Table');
        $installer->getConnection()->createTable($table);

    }
}