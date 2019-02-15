<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2016-02-01T13:09:54+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Product/Categories.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Product_Categories extends Xtento_ProductExport_Model_Export_Data_Abstract
{
    /**
     * Category cache
     */
    protected static $_categoryCache = array();

    public function getConfiguration()
    {
        // Reset cache
        self::$_categoryCache = array();

        return array(
            'name' => 'Product category information',
            'category' => 'Product',
            'description' => 'Export product categories for the given product.',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_PRODUCT),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['cats'];

        if ($this->getProfile()->getOutputType() == 'xml') {
            return $returnArray;
        }

        if (!$this->fieldLoadingRequired('cats')) {
            return $returnArray;
        }

        if (!isset(self::$_categoryCache[$this->getStoreId()])) {
            self::$_categoryCache[$this->getStoreId()] = array();
        }

        // Fetch fields to export
        $product = $collectionItem->getProduct();
        $categoryIds = $product->getCategoryIds();

        $rootCategoryId = false;
        if ($this->getStoreId()) {
            $rootCategoryId = Mage::app()->getStore($this->getStoreId())->getRootCategoryId();
        }

        $returnArray['cats'] = array();
        $this->_writeArray = & $returnArray['cats'];

        foreach ($categoryIds as $categoryId) {
            if (!array_key_exists($categoryId, self::$_categoryCache[$this->getStoreId()])
                || (array_key_exists($categoryId, self::$_categoryCache[$this->getStoreId()]) && !is_array(self::$_categoryCache[$this->getStoreId()][$categoryId]))
            ) {
                if (array_key_exists($categoryId, self::$_categoryCache[$this->getStoreId()]) && !is_array(self::$_categoryCache[$this->getStoreId()][$categoryId])) {
                    $category = self::$_categoryCache[$this->getStoreId()][$categoryId];
                } else {
                    if ($this->getStoreId()) {
                        $category = Mage::getModel('catalog/category')->setStoreId($this->getStoreId())->load($categoryId);
                    } else {
                        $category = Mage::getModel('catalog/category')->load($categoryId);
                    }
                }

                if ($rootCategoryId > 0) {
                    if (!preg_match("/1\/" . $rootCategoryId . "\//", $category->getPath())) {
                        // Category is not associated to this root category
                        continue;
                    }
                }
                $this->_writeArray = & $returnArray['cats'][];

                foreach ($category->getData() as $key => $value) {
                    $attribute = $category->getResource()->getAttribute($key);
                    $attrText = '';
                    if ($attribute) {
                        $attrText = $category->getAttributeText($key);
                    }
                    if (!empty($attrText)) {
                        $this->writeValue($key, $attrText);
                    } else {
                        $this->writeValue($key, $value);
                    }
                }

                // Build category path
                $pathIds = $category->getPathIds();
                $pathAsName = "";
                foreach ($pathIds as $pathCatId) {
                    if (array_key_exists($pathCatId, self::$_categoryCache[$this->getStoreId()])
                        && isset(self::$_categoryCache[$this->getStoreId()][$pathCatId]['name'])
                    ) {
                        $catName = self::$_categoryCache[$this->getStoreId()][$pathCatId]['name'];
                    } else {
                        if ($this->getStoreId()) {
                            $category = Mage::getModel('catalog/category')->setStoreId($this->getStoreId())->load(
                                $pathCatId
                            );
                        } else {
                            $category = Mage::getModel('catalog/category')->load($pathCatId);
                        }
                        $catName = $category->getName();
                        self::$_categoryCache[$this->getStoreId()][$pathCatId] = $category;
                    }
                    if (!empty($catName)) {
                        if (empty($pathAsName)) {
                            $pathAsName = $catName;
                        } else {
                            $pathAsName .= " > " . $catName;
                        }
                    }
                }
                $this->writeValue('path_name', $pathAsName);

                // Get product incl. category path URL
                $productUrl = $product->getUrlPath($category);
                if ($this->getProfile()->getExportUrlRemoveStore()) {
                    if (preg_match("/&/", $productUrl)) {
                        $productUrl = preg_replace("/___store=(.*?)&/", "&", $productUrl);
                    } else {
                        $productUrl = preg_replace("/\?___store=(.*)/", "", $productUrl);
                    }
                }
                $productUrl = Mage::getUrl($productUrl, array('_store' => $this->getStoreId()));
                $this->writeValue('product_url', $productUrl);

                // Cache category
                self::$_categoryCache[$this->getStoreId()][$categoryId] = $this->_writeArray;
            } else {
                // Copy from cache
                $this->_writeArray = & $returnArray['cats'][];
                $this->_writeArray = self::$_categoryCache[$this->getStoreId()][$categoryId];
            }
        }

        $this->_writeArray = & $returnArray;
        // Done
        return $returnArray;
    }
}