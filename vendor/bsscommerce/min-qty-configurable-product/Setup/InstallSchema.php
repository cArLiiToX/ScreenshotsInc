<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_MinQtyCP
 * @author     Extension Team
 * @copyright  Copyright (c) 2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\MinQtyCP\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();

		$installer->getConnection()
		->addColumn(
			$installer->getTable('cataloginventory_stock_item'),
			'bss_minimum_qty_configurable',array(
				'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'nullable' => false,
				'default' => 0.0000,
				'comment' => 'Bss Minimum Qty Configurable',
			)
		);

		$installer->getConnection()
		->addColumn(
			$installer->getTable('cataloginventory_stock_item'),
			'use_config_bss_minimum_qty_configurable',array(
				'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
				'unsigned' => true,
				'nullable' => false,
				'default' => 1,
				'comment' => 'Bss Use Config Bss Minimum Qty Configurable',
			)
		);

		$installer->endSetup();
	}
}
