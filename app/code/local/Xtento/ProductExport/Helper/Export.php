<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2016-09-07T18:55:04+02:00
 * File:          app/code/local/Xtento/ProductExport/Helper/Export.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Helper_Export extends Mage_Core_Helper_Abstract
{
    public function getExportEntity($entity)
    {
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            return 'catalog/product';
        } else if ($entity == Xtento_ProductExport_Model_Export::ENTITY_REVIEW) {
            return 'review/review';
        } else if ($entity == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            return 'catalog/category';
        }
        Mage::throwException(Mage::helper('xtento_productexport')->__('Could not find export entity "%s"', $entity));
    }

    public function getLastEntityId($entity)
    {
        $collection = Mage::getModel($this->getExportEntity($entity))->getCollection();
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT || $entity == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            $collection->addAttributeToSelect('entity_id');
            $collection->getSelect()->limit(1)->order('entity_id DESC');
        }
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_REVIEW) {
            $collection->getSelect()->limit(1)->order('main_table.review_id DESC');
        }
        $object = $collection->getFirstItem();
        return $object->getId();
    }

    public function getExportBkpDir()
    {
        return Mage::getBaseDir('var') . DS . "product_export_bkp" . DS;
    }
}