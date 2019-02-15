<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2018-02-20T13:40:30+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Abstract.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_ProductExport_Model_Export_Data_Abstract extends Mage_Core_Model_Abstract implements Xtento_ProductExport_Model_Export_Data_Interface
{
    protected $_cache;
    protected $_profile;
    protected $_writeArray;
    protected $_fieldsToFetch = false;
    protected $_fieldsNotFound = array();
    protected $_fieldsFound = array();

    protected function _construct()
    {
        $this->initConfiguration($this->getConfiguration());
    }

    protected function initConfiguration($configuration)
    {
        foreach ($configuration as $key => $value) {
            $this->setData($key, $value);
        }
        return $this;
    }

    /*
     * Checks XSL template if field X is used there at all and thus if it should be fetched to avoid unnecessary DB queries and memory usage.
     */
    protected function _initFieldsToFetch()
    {
        $this->_fieldsToFetch = array();
        if ($this->getProfile()->getOutputType() == 'csv' || $this->getProfile()->getOutputType() == 'xml') {
            // Fetch all fields
            return $this;
        }
        $xslTemplate = $this->getProfile()->getXslTemplate();
        if (!strstr($xslTemplate, '<file') && @file_exists($xslTemplate)) {
            // XSL Template is loaded from file, fetch fields from there
            $xslTemplate = @file_get_contents($xslTemplate);
            if (empty($xslTemplate)) {
                return $this; // Fetch all fields
            }
        }
        preg_match_all("/(select=\"([^\"]+)\"|test=\"([^\"]+)\")/", $xslTemplate, $fieldMatches);
        if (isset($fieldMatches[1])) {
            foreach ($fieldMatches[1] as $fieldMatch) {
                if (!in_array($fieldMatch, $this->_fieldsToFetch)) {
                    array_push($this->_fieldsToFetch, $fieldMatch);
                }
            }
        }
        #var_dump($this->getProfile()->getId());
        #var_dump($this->_fieldsToFetch);
        array_push($this->_fieldsToFetch, 'entity_id');
        array_push($this->_fieldsToFetch, 'created_at');
        #var_dump($fieldMatches[1], $this->_fieldsToFetch); die();
        return $this;
    }

    /*
     * Check if field should be fetched from the DB
     */
    protected function fieldLoadingRequired($field)
    {
        #return true;
        if ($this->_fieldsToFetch === false) {
            $this->_initFieldsToFetch();
        }
        if (empty($this->_fieldsToFetch) || $this->getShowEmptyFields()) {
            return true;
        }
        $fieldHash = md5($field);
        if (isset($this->_fieldsNotFound[$fieldHash])) {
            return false;
        }
        if (isset($this->_fieldsFound[$fieldHash])) {
            return true;
        }
        if (!in_array($field, $this->_fieldsToFetch)) {
            foreach ($this->_fieldsToFetch as $fieldToFetch) {
                if (stristr($fieldToFetch, $field)) {
                    $this->_fieldsFound[$fieldHash] = true;
                    return true;
                }
            }
            $this->_fieldsNotFound[$fieldHash] = true;
            return false;
        }
        $this->_fieldsFound[$fieldHash] = true;
        return true;
    }

    /*
     * Is "depends_module" an installed module/extension?
     */
    public function confirmDependency()
    {
        if (!$this->getDependsModule()) {
            return true;
        }
        return Mage::helper('xtcore/utils')->isExtensionInstalled($this->getDependsModule()) && Mage::helper('core')->isModuleEnabled($this->getDependsModule());
    }

    protected function writeValue($field, $value, $customWriteArray = false)
    {
        if ($this->fieldLoadingRequired($field) && !is_object($value)) {
            if (($field !== NULL && !is_array($value) && $value !== NULL && $value !== '') || ($this->getShowEmptyFields() && !is_array($value))) {
                if ($this->getProfile()->getExportReplaceNlBr() != 0) {
                    if ($this->getProfile()->getExportReplaceNlBr() == 3) {
                        $value = str_replace(array("\r\n", "\r", "\n"), "", $value);
                    } else if ($this->getProfile()->getExportReplaceNlBr() == 2) {
                        $value = str_replace(array("\r\n", "\r", "\n"), " ", $value);
                    } else if ($this->getProfile()->getExportReplaceNlBr() == 1) {
                        $value = str_replace(array("\r\n", "\r", "\n"), "<br />", $value);
                    }
                }
                if ($this->getProfile() && $this->getProfile()->getExportStripTags()) {
                    $value = strip_tags($value);
                }
                if (!$customWriteArray) {
                    $this->_writeArray[$field] = $value;
                } else {
                    $this->_writeArray[$customWriteArray][$field] = $value;
                }
            } else if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (!is_array($v) && !is_object($v)) {
                        if ($this->getProfile() && $this->getProfile()->getExportStripTags()) {
                            $v = strip_tags($v);
                        }
                        $this->writeValue($k, $v, $field);
                    }
                }
            }
        }
    }

    /*
     * Get store ID of export profile
     */
    protected function getStoreId()
    {
        $storeId = 0;
        if ($this->getProfile() && $this->getProfile()->getStoreIds()) {
            $storeId = $this->getProfile()->getStoreIds();
        }
        return $storeId;
    }
}