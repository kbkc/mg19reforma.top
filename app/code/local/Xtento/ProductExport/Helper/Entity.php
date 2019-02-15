<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2016-09-06T22:05:42+02:00
 * File:          app/code/local/Xtento/ProductExport/Helper/Entity.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Helper_Entity extends Mage_Core_Helper_Abstract
{
    public function getPluralEntityName($entity) {
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_CATEGORY) {
            return "categories";
        }
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_PRODUCT) {
            return "products";
        }
        if ($entity == Xtento_ProductExport_Model_Export::ENTITY_REVIEW) {
            return "product reviews";
        }
        return $entity;
    }
}