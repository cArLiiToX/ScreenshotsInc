<?php

use Magento\Framework\App\Bootstrap;
include_once 'function.php';
require_once 'xeconfig.php';
$redirect_path = XEPATH . 'xetool/index.php';

include '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$attribute_set_name = 'inkXE';
$xe_color = 'xe_color';
$clabel = 'Color';
$xe_size = 'xe_size';
$slabel = 'Size';
$xe_is_designer = 'xe_is_designer';
$dlabel = 'Show in Designer';
$xe_is_template = 'xe_is_template';
$tlabel = 'Pre Decorated Product';

$installer = $objectManager->create('Magento\Eav\Model\Entity\Attribute\Set');
$attributeSetId = $installer->load($attribute_set_name, 'attribute_set_name')->getAttributeSetId();

if (empty($attributeSetId) || !isset($attributeSetId)) {
    createAttributeSet($attribute_set_name);
}

$attributeobj = $objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute');
$attribute = $objectManager->create('Magento\Catalog\Model\Entity\Attribute');
$options = $objectManager->create('Magento\Eav\Setup\EavSetupFactory');
$groupId = $installer->load($attribute_set_name, 'attribute_set_name')->getDefaultGroupId();

//create color attribute
$attributeColorId = $attributeobj->getIdByCode('catalog_product', $xe_color);
if (!$attributeColorId) {
    $attributeColorData = [
        'entity_type_id' => 4,
        'attribute_set_id' => $attributeSetId,
        'attribute_group_id' => $groupId,
        'attribute_code' => $xe_color,
        'frontend_input' => 'select',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'frontend_label' => $clabel,
        'backend_type' => 'varchar',
        'is_required' => 0,
        'is_user_defined' => 1,
    ];

    $attribute->setData($attributeColorData);
    $attribute->save();
    $attribute_arr = array('Red', 'Blue', 'Yellow');
    $option = array();
    $attributeColorId = $attributeobj->getIdByCode('catalog_product', $xe_color);
    $option['attribute_id'] = $attributeColorId;
    foreach ($attribute_arr as $key => $value) {
        $option['value'][$value][0] = $value;
    }
    $eavSetup = $options->create();
    $eavSetup->addAttributeOption($option);
}

//create size attribute
$attributeSizeId = $attributeobj->getIdByCode('catalog_product', $xe_size);
if (!$attributeSizeId) {
    $attributeSizeData = [
        'entity_type_id' => 4,
        'attribute_set_id' => $attributeSetId,
        'attribute_group_id' => $groupId,
        'attribute_code' => $xe_size,
        'frontend_input' => 'select',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'frontend_label' => $slabel,
        'backend_type' => 'varchar',
        'is_required' => 0,
        'is_user_defined' => 1,
    ];

    $attribute->setData($attributeSizeData);
    $attribute->save();
    $attribute_arr = array('L', 'M', 'S');
    $option = array();
    $attributeSizeId = $attributeobj->getIdByCode('catalog_product', $xe_size);
    $option['attribute_id'] = $attributeSizeId;
    foreach ($attribute_arr as $key => $value) {
        $option['value'][$value][0] = $value;
    }
    $eavSetup = $options->create();
    $eavSetup->addAttributeOption($option);
}

//create isdesigner attribute
$attributeDesignerId = $attributeobj->getIdByCode('catalog_product', $xe_is_designer);
if (!$attributeDesignerId) {
    $attributeDesignerData = array(
        'entity_type_id' => 4,
        'attribute_code' => $xe_is_designer,
        'frontend_input' => 'boolean',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'frontend_label' => $dlabel,
        'backend_type' => 'int',
        'is_required' => 1,
        'is_user_defined' => 1,
        'attribute_set_id' => $attributeSetId,
        'attribute_group_id' => $groupId,
        'apply_to' => 'configurable',
    );
    $attribute->setData($attributeDesignerData);
    $attribute->save();
    $attributeDesignerId = $attributeobj->getIdByCode('catalog_product', $xe_is_designer);
}

//create isdesigner attribute
$attributeTemplateId = $attributeobj->getIdByCode('catalog_product', $xe_is_template);
if (!$attributeTemplateId) {
    $attributeTemplateData = array(
        'entity_type_id' => 4,
        'attribute_code' => $xe_is_template,
        'frontend_input' => 'boolean',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'frontend_label' => $tlabel,
        'backend_type' => 'int',
        'is_required' => 0,
        'is_user_defined' => 1,
        'attribute_set_id' => $attributeSetId,
        'attribute_group_id' => $groupId,
        'apply_to' => 'configurable',
    );
    $attribute->setData($attributeTemplateData);
    $attribute->save();
    $attributeTemplateId = $attributeobj->getIdByCode('catalog_product', $xe_is_template);
}

//Assign attributes to attribute set
$attribute_code = array($attributeColorId => $xe_color, $attributeSizeId => $xe_size, $attributeDesignerId => $xe_is_designer, $attributeTemplateId => $xe_is_template);
$installer1 = $objectManager->create('Magento\Eav\Setup\EavSetup');
$attribute_set_id = $installer1->getAttributeSetId('catalog_product', $attribute_set_name);
$group_name = 'General';
$attribute_group_id = $installer1->getAttributeGroupId('catalog_product', $attribute_set_id, $group_name);
$entityTypeId = $objectManager->create('Magento\Eav\Model\Entity\Type')->loadByCode('catalog_product')->getId();

try {
    foreach ($attribute_code as $k => $att) {
        if ($att == $xe_is_designer || $att == $xe_is_template) {
            $installer1->updateAttribute('catalog_product', $att, array('is_required' => false, 'apply_to' => 'configurable'));
        }
        if ($att == $xe_color || $att == $xe_size) {
            $installer1->updateAttribute('catalog_product', $att, array('required' => 1, 'is_html_allowed_on_front' => 1, 'is_visible_on_front' => 1, 'used_in_product_listing' => true));
        }
        $installer1->addAttributeToSet($entityTypeId, $attribute_set_id, $attribute_group_id, $k);
    }

} catch (Exception $e) {
    xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 2nd Step: ' . $e->getMessage() . "\n");
    $msg = 'Unable to associate attributes to attribute set.';
    header('Location: ' . $redirect_path . '?action=attr&msg=' . $msg);exit(0);
}

//create attribute set name
function createAttributeSet($setName)
{
    try {
        $bootstrap = Bootstrap::create(BP, $_SERVER);
        $objectManager = $bootstrap->getObjectManager();
        $attributeSet = $objectManager->create('Magento\Eav\Model\Entity\Attribute\Set');
        $entityTypeId = $objectManager->create('Magento\Eav\Model\Entity\Type')->loadByCode('catalog_product')->getId();
        $attributeSet->setData(array(
            'attribute_set_name' => $setName,
            'entity_type_id' => $entityTypeId,
            'sort_order' => 200,
        ));
        $attributeSet->validate();
        $attributeSet->save();
        $baseSetId = 4;
        $attributeSet->initFromSkeleton($baseSetId)->save();
    } catch (Exception $e) {
        $msg = 'Your magento is unable to create attribute set.';
        xe_log("\n" . date("Y-m-d H:i:s") . ': Error in 2nd Step: ' . $e->getMessage() . "\n");
        header('Location: ' . $redirect_path . '?action=attr&msg=' . $msg);exit(0);
    }
}

header('Location: ' . $redirect_path . '?action=cms');
