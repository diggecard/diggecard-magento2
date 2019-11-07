<?php
/**
 * @author DiggEcard Team
 * @copyright Copyright (c) 2019 DiggEcard (https://diggecard.com)
 */

namespace Diggecard\Giftcard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Diggecard\Giftcard\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $installer = $setup;

        $installer->startSetup();

        $connection = $installer->getConnection();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $connection->addColumn(
                $installer->getTable('quote'),
                'diggecard_giftcard_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 30,
                    'nullable' => true,
                    'comment' => 'Giftcard ID'
                ]
            );

            $connection->addColumn(
                $installer->getTable('quote'),
                'diggecard_giftcard_discount',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 30,
                    'nullable' => true,
                    'comment' => 'Giftcard ID'
                ]
            );

            $connection->addColumn(
                $installer->getTable('quote'),
                'diggecard_giftcard_base_discount',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 30,
                    'nullable' => true,
                    'comment' => 'Giftcard ID'
                ]
            );

            $connection->addColumn(
                $installer->getTable('quote'),
                'diggecard_giftcard_reservation_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Giftcard Amount Reservation ID'
                ]
            );


            $connection->addColumn(
                $installer->getTable('sales_order'),
                'dg_giftcard_amount_invoiced',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 30,
                    'nullable' => true,
                    'comment' => 'Giftcard Amount Invoiced'
                ]
            );

            $connection->addColumn(
                $installer->getTable('sales_order'),
                'dg_giftcard_base_amount_invoiced',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 30,
                    'nullable' => true,
                    'comment' => 'Giftcard Amount Invoiced'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.10', '<')) {
            $connection->changeColumn(
                $installer->getTable('quote'),
                'diggecard_giftcard_id',
                'diggecard_giftcard_id',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '30,2',
                    'nullable' => true,
                    'comment' => 'Giftcard ID'
                ]
            );

            $connection->changeColumn(
                $installer->getTable('quote'),
                'diggecard_giftcard_discount',
                'diggecard_giftcard_discount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '30,2',
                    'nullable' => true,
                    'comment' => 'Giftcard ID'
                ]
            );

            $connection->changeColumn(
                $installer->getTable('quote'),
                'diggecard_giftcard_base_discount',
                'diggecard_giftcard_base_discount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '30,2',
                    'nullable' => true,
                    'comment' => 'Giftcard ID'
                ]
            );

            $connection->changeColumn(
                $installer->getTable('sales_order'),
                'dg_giftcard_amount_invoiced',
                'dg_giftcard_amount_invoiced',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '30,2',
                    'nullable' => true,
                    'comment' => 'Giftcard Amount Invoiced'
                ]
            );

            $connection->changeColumn(
                $installer->getTable('sales_order'),
                'dg_giftcard_base_amount_invoiced',
                'dg_giftcard_base_amount_invoiced',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '30,2',
                    'nullable' => true,
                    'comment' => 'Giftcard Amount Invoiced'
                ]
            );
        }

        $installer->endSetup();
    }
}