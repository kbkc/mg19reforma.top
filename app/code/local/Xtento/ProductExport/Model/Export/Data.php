<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2018-02-20T13:40:25+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data extends Mage_Core_Model_Abstract
{
    private $_registeredExportData = null;

    protected function _getRegisteredExportData()
    {
        $this->_registeredExportData = array();
        $etcDir = Mage::helper('xtcore/filesystem')->getModuleDir($this);
        // Load registered export data
        $exportDataFile = $etcDir . DS . 'xtento' . DS . 'export_data.xml';
        $this->_loadExportDataFile($exportDataFile);
        // Users own export data file
        $exportOwnDataFile = $etcDir . DS . 'xtento' . DS . 'export_data.own.xml';
        $this->_loadExportDataFile($exportOwnDataFile, false);
        #var_dump($this->_registeredExportData); die();
    }


    protected function _loadExportDataFile($exportDataFile, $throwFileException = true)
    {
        if (file_exists($exportDataFile) || is_readable($exportDataFile)) {
            $exportData = simplexml_load_file($exportDataFile);
            if ($exportData) {
                foreach ($exportData->data->children() as $dataIdentifier => $dataConfig) {
                    $profileIds = (string)$dataConfig->profile_ids; // Apply class only to profile IDs X,Y,Z (comma-separated)
                    if ($profileIds !== '') {
                        if ($this->getProfile() && in_array($this->getProfile()->getId(), explode(",", $profileIds))) {
                            $this->_registeredExportData[$dataIdentifier] = $dataConfig;
                        }
                    } else {
                        //array_push($this->_registeredExportData, array('name' => $exportName, 'config' => $dataConfig));
                        $this->_registeredExportData[$dataIdentifier] = $dataConfig;
                    }
                }
            } else {
                Mage::throwException('Could not load export_data.xml file for data exporting. File broken? Location: ' . $exportDataFile);
            }
        } else {
            if ($throwFileException) {
                Mage::throwException('Could not load export_data.xml file for data exporting. File does not exist or is not readable. Location: ' . $exportDataFile);
            }
        }
    }

    public function getExportData($entityType, $collectionItem = false, $getConfiguration = false)
    {
        if ($this->_registeredExportData === null) {
            $this->_getRegisteredExportData();
        }
        $exportData = array();
        foreach ($this->_registeredExportData as $dataIdentifier => $dataConfig) {
            $className = @current($dataConfig->class);
            if (!$className) {
                $className = (string)$dataConfig->class;
            }
            $classIdentifier = str_replace('xtento_productexport/export_data_', '', $className);
            $exportClass = Mage::getSingleton($className);
            if ($exportClass) {
                #$memBefore = memory_get_usage();
                #echo "Before - ".$dataConfig->class.": $memBefore<br>";
                if ($getConfiguration) {
                    if ($exportClass->getEnabled() && $exportClass->confirmDependency() && in_array($entityType, $exportClass->getApplyTo())) {
                        $exportData[] = array('class' => $className, 'class_identifier' => $classIdentifier, 'configuration' => $exportClass->getConfiguration());
                    }
                } else {
                    if (!in_array($entityType, $exportClass->getApplyTo())) {
                        continue;
                    }
                    if (!$exportClass->getEnabled() || !$exportClass->confirmDependency()) {
                        continue;
                    }
                    $returnData = $exportClass
                        ->setProfile($this->getProfile())
                        ->setShowEmptyFields($this->getShowEmptyFields())
                        ->getExportData($entityType, $collectionItem);
                    if (is_array($returnData)) {
                        $exportData = array_merge_recursive($exportData, $returnData);
                    }
                }
                #echo "After: ".memory_get_usage()." (Difference: ".round((memory_get_usage() - $memBefore) / 1024 / 1024, 2)." MB)<br>";
            }
        }
        #Zend_Debug::dump($collectionItem); die();
        #if ($this->getProfile()->getId() == 6) {
            #var_dump($exportData);
            #die();
        #}
        $exportData = array_merge_recursive($exportData, $this->_addPrivateFields($collectionItem, $exportData));
        return $exportData;
    }

    /*
     * As data export classes are instantiated using getSingleton, we need to reset them for each new profile exported so now old data is retained in the export classes
     */
    public function resetExportClasses()
    {
        if ($this->_registeredExportData === null) {
            $this->_getRegisteredExportData();
        }
        foreach ($this->_registeredExportData as $dataIdentifier => $dataConfig) {
            $className = @current($dataConfig->class);
            Mage::unregister('_singleton/' . $className);
        }
    }

    private function _addPrivateFields($collectionItem, $exportData)
    {
        $privateFields = array();
        if ($collectionItem !== FALSE && $collectionItem->getObject()) {
            if (!isset($exportData['entity_id'])) {
                $privateFields['entity_id'] = $collectionItem->getObject()->getId();
            }
            if (!isset($exportData['store_id'])) {
                #$privateFields['store_id'] = $collectionItem->getObject()->getStoreId();
            }
            if (!isset($exportData['created_at'])) {
                $privateFields['created_at'] = $collectionItem->getObject()->getCreatedAt();
            }
        }
        return $privateFields;
    }
}