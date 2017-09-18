<?php

namespace Mageplaza\Seo\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{


    /**
     * upgrade
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            $this->addColumn($installer);
        }
        $setup->endSetup();
    }

    public function addColumn($installer)
    {
        $tableName = $installer->getTable('cms_page');
        $columns   = [
            'mp_meta_robots' => [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                'nullable' => true,
                'comment'  => 'Meta robot',
            ],
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($tableName, $name, $definition);
        }

        $tableName = $installer->getTable('store');
        $columns   = [
            'mp_lang' => [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment'  => 'language',
            ],
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($tableName, $name, $definition);
        }
    }
}
