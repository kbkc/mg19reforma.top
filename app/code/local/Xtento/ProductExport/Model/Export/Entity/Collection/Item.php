<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2016-09-06T22:35:59+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Entity/Collection/Item.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Entity_Collection_Item extends Varien_Object
{
    private $_collectionItem;

    public function __construct($collectionItem, $entityType, $currItemNo, $collectionCount)
    {
        $this->_collectionItem = $collectionItem;
        $this->_collectionSize = $collectionCount;
        $this->_currItemNo = $currItemNo;
        if ($entityType == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            $this->setProduct($collectionItem);
        }
        if ($entityType == Xtento_ProductExport_Model_Export::ENTITY_REVIEW) {
            $this->setReview($collectionItem);
        }
        if ($entityType == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            $this->setCategory($collectionItem);
        }
    }

    public function getObject() {
        return $this->_collectionItem;
    }
}