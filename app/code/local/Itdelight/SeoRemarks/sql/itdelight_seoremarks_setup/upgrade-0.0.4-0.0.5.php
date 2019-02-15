<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
$entityType = 'catalog_product';
$attrCode = 'shipment';
$installer->startSetup();
//$installer->removeAttribute('catalog_product', 'shipment');
$shipAttr = Mage::getResourceModel('eav/entity_attribute')
    ->getIdByCode('catalog_product', 'shipment');
if (!$shipAttr) {
    $attribute = array(
        'group' => 'General',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Shipment',
        'source' => 'eav/entity_attribute_source_table',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => 1,
        'required' => 0,
        'visible_on_front' => 1,
        'is_html_allowed_on_front' => 0,
        'is_configurable' => 0,
        'searchable' => 0,
        'filterable' => 0,
        'comparable' => 0,
        'unique' => false,
        'user_defined' => true,
        'used_in_product_listing' => true,
    );
    $installer->addAttribute($entityType, $attrCode, $attribute);

    $frontLabels = array(Mage::getModel('core/store')->load('ru', 'code')->getId() => 'Доставка',
        Mage::getModel('core/store')->load('ua', 'code')->getId() => 'Доставка');

    $attributeId = $installer->getAttributeId($entityType, $attrCode);
    Mage::getSingleton('eav/config')->getAttribute($entityType, $attributeId)
        ->setData('store_labels', $frontLabels)
        ->save();
}

$indexCollection = Mage::getModel('index/process')->getCollection();
foreach ($indexCollection as $index) {
    /* @var $index Mage_Index_Model_Process */
    $index->reindexAll();
}

$installer->endSetup();