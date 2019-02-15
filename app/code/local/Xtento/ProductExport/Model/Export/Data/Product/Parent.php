<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2017-07-06T13:46:48+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Product/Parent.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Product_Parent extends Xtento_ProductExport_Model_Export_Data_Product_General
{
    /**
     * Parent product cache
     */
    protected static $_parentProductCache = array();

    public function getConfiguration()
    {
        // Reset cache
        self::$_parentProductCache = array();

        return array(
            'name' => 'Parent item information',
            'category' => 'Product',
            'description' => 'Export parent item',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        // Fetch product - should be a child
        $product = $collectionItem->getProduct();

        if ($this->getProfile()->getOutputType() == 'xml') {
            return $returnArray;
        }

        $parentId = -1;
        // Check if it's a child product, and if yes, find & export parent id
        if ($this->fieldLoadingRequired('parent_id')) {
            $this->_writeArray = & $returnArray; // Write on product level
            $parentId = $this->_getFirstParentProductId($product);
            $this->writeValue('parent_id', $parentId);
        }

        // Find & export parent item
        if ($this->fieldLoadingRequired('parent_item') || $this->fieldLoadingRequired('option_parameters_in_url')) {
            $returnArray['parent_item'] = $this->_getParentData($product, $parentId, 0);

            $this->_writeArray = & $returnArray; // Write on product level
        }

        // Done
        return $returnArray;
    }

    /**
     * get the parent data as array
     * if the parent has also a parent, its data is exported as well
     * this function changes the $_writeArray reference     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $parentId [optional = -1]
     * @return array
     */
    protected function _getParentData($product, $parentId = -1, $depth = 0)
    {
        $data = array();

        // load parent id

        if ($parentId == -1) {
            $parentId = $this->_getFirstParentProductId($product);
        }
        if (!$parentId)
            return $data;

        // check cache

        if (!isset(self::$_parentProductCache[$this->getStoreId()])) {
            self::$_parentProductCache[$this->getStoreId()] = array();
        }

        if (array_key_exists($parentId, self::$_parentProductCache[$this->getStoreId()]) && !$this->fieldLoadingRequired('option_parameters_in_url')) {
            return self::$_parentProductCache[$this->getStoreId()][$parentId];
        }

        // load parent

        if ($this->getStoreId()) {
            $parent = Mage::getModel('catalog/product')->setStoreId($this->getStoreId())->load($parentId);
        } else {
            $parent = Mage::getModel('catalog/product')->load($parentId);
        }
        if ($parent && $parent->getId()) {

            $this->_writeArray = & $data; // Write on parent_item level

            if ($this->fieldLoadingRequired('option_parameters_in_url') && $parent->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $superAttributesWithValues = array();
                $superAttributes = $parent->getTypeInstance(true)->getConfigurableAttributes($parent);
                foreach ($superAttributes as $superAttribute) {
                    $superAttributeId = $superAttribute->getProductAttribute()->getId();
                    $superAttributeCode = $superAttribute->getProductAttribute()->getAttributeCode();
                    $superAttributeValues = $superAttribute->getPrices() ? $superAttribute->getPrices() : array();
                    foreach ($superAttributeValues as $superAttributeValue) {
                        if ($superAttributeValue['value_index'] == $product->getData($superAttributeCode)) {
                            $superAttributesWithValues[] = $superAttributeId . "=" . $superAttributeValue['value_index'];
                        }
                    }
                }
                $this->writeValue('option_parameters_in_url', implode("&", $superAttributesWithValues));
            }
            // Export product data of parent product
            $this->_exportProductData($parent, $data);
            $this->writeValue('entity_id', $parent->getId());
            if ($this->fieldLoadingRequired('parent_item/cats')) {
                // Export categories for parent product
                $fakedCollectionItem = new Varien_Object();
                $fakedCollectionItem->setProduct($parent);
                $exportClass = Mage::getSingleton('xtento_productexport/export_data_product_categories');
                $exportClass->setProfile($this->getProfile());
                $exportClass->setShowEmptyFields($this->getShowEmptyFields());
                $returnData = $exportClass->getExportData(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT, $fakedCollectionItem);
                if (is_array($returnData) && !empty($returnData)) {
                    $this->_writeArray = array_merge_recursive($this->_writeArray, $returnData);
                }
            }

            // Parent's parent
            $grandParentId = $this->_getFirstParentProductId($parent);
            if ($grandParentId && $grandParentId != $parent->getId() && $depth < 5) { // Maximum 5 parent products to avoid recursion
                $depth++;
                $data['parent_item'] = $this->_getParentData($parent, $grandParentId, $depth);
            }
        }

        // Cache parent product
        self::$_parentProductCache[$this->getStoreId()][$parentId] = $data;

        return $data;
    }

    /**
     * Get parent id of the product
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    protected function _getFirstParentProductId($product)
    {
        $parentId = null;
        #if ($product->getTypeId() == 'simple') {
            $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
            if (!$parentIds) {
                $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
            }
            foreach ($parentIds as $possibleParentId) {
                // Check if parent product exists, if yes return first existing parent product
                $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
                $select = $readAdapter->select()
                    ->from(Mage::getSingleton('core/resource')->getTableName('catalog/product'), array('entity_id'))
                    ->where("entity_id = ?", $possibleParentId);
                $products = $readAdapter->fetchAll($select);
                if (count($products) > 0) {
                    $parentId = $possibleParentId;
                }
            }
        #}

        return (int)$parentId;
    }
}