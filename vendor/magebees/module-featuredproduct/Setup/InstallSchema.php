<?php
/***************************************************************************
 Extension Name	: Featured Products 
 Extension URL	: https://www.magebees.com/featured-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Featuredproduct\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

          $table = $installer->getConnection()->newTable(
              $installer->getTable('magebees_featuredproducts')
          )->addColumn(
              'id',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              null,
              ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
              'id'
          )->addColumn(
              'entity_id',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              null,
              ['unsigned' => true,'nullable' => false],
              'entity_id'
          )->addColumn(
              'store_id',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              11,
              ['unsigned' => true,'nullable' => false],
              'store_id'
          );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
