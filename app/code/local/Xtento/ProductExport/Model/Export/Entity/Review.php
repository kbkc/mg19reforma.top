<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2017-02-13T16:44:05+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Entity/Review.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Entity_Review extends Xtento_ProductExport_Model_Export_Entity_Abstract
{
    protected $_entityType = Xtento_ProductExport_Model_Export::ENTITY_REVIEW;

    protected function _construct()
    {
        $collection = Mage::getModel('review/review')->getResourceCollection();
        //->addReviewsTotalCount(); // Caused issues with collection->getSize()

        $this->_collection = $collection;
        parent::_construct();
    }

    public function runExport()
    {
        if ($this->getProfile()) {
            if ($this->getProfile()->getStoreIds()) {
                $this->_collection->addStoreFilter($this->getProfile()->getStoreIds());
            }
        }
        $this->_collection->addRateVotes();
        return $this->_runExport();
    }

    public function setCollectionFilters($filters)
    {
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                foreach ($filter as $attribute => $filterArray) {
                    $this->_collection->addFieldToFilter($attribute, $filterArray);
                }
            }
        }
        return $this->_collection;
    }
}