<?php


namespace Madepeople\Test\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $setup->startSetup();
        if (!$setup->tableExists('madepeople_test')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('madepeople_test'));
            $table->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'ID'
            )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'default' => null,
                        'unique' => true
                    ],
                    'Order ID'
                )
                ->addColumn(
                    'multiplied_sum',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Order Multiplied Sum'
                );

            $setup->getConnection()->createTable($table);


            $setup->endSetup();
        }
    }
}
