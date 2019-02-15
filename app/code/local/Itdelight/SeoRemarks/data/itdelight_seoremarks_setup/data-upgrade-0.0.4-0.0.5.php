<?php
$skuAttr = Mage::getModel('catalog/resource_eav_attribute');
$skuAttr->loadByCode('catalog_product', 'sku');
if ($skuAttr) {
    $skuAttr->setIsVisibleOnFront(1)
        ->save();
}