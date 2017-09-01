<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WeltPixel\OwlCarouselSlider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the OwlCarouselSlider module DB scheme
 * Add new column to weltpixel_owlcarouselslider_banners table
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
    
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'custom_css',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '64k',
                    'nullable' => true,
                    'after'    => 'custom_content',
                    'comment'  => 'Custom CSS'
                ]
            );
        }
        
        $setup->endSetup();
    }
}
