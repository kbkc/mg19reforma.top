<?php
$catCollection = Mage::getModel('catalog/category')->getCollection();
$resource = Mage::getResourceModel('catalog/category');
$search = ['<strong>', 'strong>'];
$replace = ['<span class="bold">', 'span>'];

/** @var Mage_Core_Model_Resource_Setup $setup */
$setup = $this;

$attributeDescriptionId = Mage::getResourceModel('eav/entity_attribute')
    ->getIdByCode('catalog_category', 'description');

foreach ($catCollection as $cat) {
    $storeIds = $cat->getStoreIds();

    foreach ($storeIds as $storeId) {
        $oldDescSql = "SELECT `value_id`, `value` "
            . " FROM {$setup->getTable('catalog_category_entity_text')} WHERE `store_id` = {$storeId}"
            . " AND `entity_id` = {$cat->getId()}"
            . " AND  `attribute_id` = {$attributeDescriptionId}";
        $descriptionOld = $setup->getConnection()->fetchRow($oldDescSql);
        if ($descriptionOld && $descriptionOld['value']) {
            $descriptionNew = addslashes(str_replace($search, $replace, $descriptionOld['value']));
            $newDescSql = "UPDATE {$setup->getTable('catalog_category_entity_text')} "
                . "SET `value` = '{$descriptionNew}'  WHERE `value_id` = {$descriptionOld['value_id']}";

            $setup->getConnection()->query($newDescSql);
        }
    }
}

Mage::getConfig()->saveConfig('creareseocore/metadata/category_title', '[original_name]');