<?php
namespace Html5design\Cedapi\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        //$installer = $setup;
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $connection = $setup->getConnection();

            $tableNames = array('quote_item');
            // Declare data
            $columns = array(
                'custom_design' => array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'LENGTH' => 10,
                    'comment' => 'custom design',
                ),
            );
            $connection = $setup->getConnection();
            foreach ($tableNames as $tableName) {
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            $tableNames = array('sales_order_item');
            // Declare data
            $columns = array(
                'custom_design' => array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'LENGTH' => 10,
                    'comment' => 'custom design',
                ),
            );
            $connection = $setup->getConnection();
            foreach ($tableNames as $tableName) {
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

        }
    }
}
