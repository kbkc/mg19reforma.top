<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2017-06-28T22:25:08+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Category/General.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Category_General extends Xtento_ProductExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'General category information',
            'category' => 'Category',
            'description' => 'Export extended category information.',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_CATEGORY),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray; // Write directly on category level
        // Fetch fields to export
        $category = $collectionItem->getCategory();

        // Timestamps of creation/update
        if ($this->fieldLoadingRequired('created_at_timestamp')) $this->writeValue('created_at_timestamp', Mage::helper('xtento_productexport/date')->convertDateToStoreTimestamp($category->getCreatedAt()));
        if ($this->fieldLoadingRequired('updated_at_timestamp')) $this->writeValue('updated_at_timestamp', Mage::helper('xtento_productexport/date')->convertDateToStoreTimestamp($category->getUpdatedAt()));

        // Which line is this?
        $this->writeValue('line_number', $collectionItem->_currItemNo);
        $this->writeValue('count', $collectionItem->_collectionSize);

        // Export information
        $this->writeValue('export_id', (Mage::registry('product_export_log')) ? Mage::registry('product_export_log')->getId() : 0);

        $this->_exportCategoryData($category);

        // Done
        return $returnArray;
    }

    /**
     * @param $category Mage_Catalog_Model_Category
     */
    protected function _exportCategoryData($category)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            $category->setStoreId($storeId);
            $this->writeValue('store_id', $storeId);
        } else {
            $this->writeValue('store_id', 0);
        }
        foreach ($category->getData() as $key => $value) {
            if ($key == 'entity_id') {
                continue;
            }
            if (!$this->fieldLoadingRequired($key)) {
                continue;
            }
            $attribute = $category->getResource()->getAttribute($key);
            $attrText = '';
            if ($attribute) {
                try {
                    $attrText = $category->getAttributeText($key);
                } catch (Exception $e) {
                    //echo "Problem with attribute $key: ".$e->getMessage();
                    continue;
                }
            }
            if (!empty($attrText)) {
                $this->writeValue($key, $attrText);
            } else {
                $this->writeValue($key, $value);
            }
        }

        // Extended fields
        if ($this->fieldLoadingRequired('category_url')) {
            $urlModel = $category->getUrlModel();
            if ($storeId && $urlModel) {
                $urlModel->getUrlInstance()->setStore($storeId);
            }
            $this->writeValue('category_url', $category->getUrl());
        }
    }
}