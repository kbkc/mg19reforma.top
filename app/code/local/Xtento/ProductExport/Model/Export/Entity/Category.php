<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2015-01-22T21:57:48+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Entity/Category.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Entity_Category extends Xtento_ProductExport_Model_Export_Entity_Abstract
{
    protected $_entityType = Xtento_ProductExport_Model_Export::ENTITY_CATEGORY;

    protected function _construct()
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*');
        $this->_collection = $collection;
        parent::_construct();
    }

    public function runExport()
    {
        if ($this->getProfile()) {
            $storeId = $this->getProfile()->getStoreIds();
            if ($storeId) {
                $rootCategory = Mage::getModel('catalog/category')
                    ->setStoreId($storeId)
                    ->load(Mage::app()->getStore($storeId)->getRootCategoryId());
                $this->_collection->addAttributeToFilter('path', array('like' => $rootCategory->getPath() . '/%'));
                $this->_collection->setStoreId($storeId);
            }
        }
        return $this->_runExport();
    }
}