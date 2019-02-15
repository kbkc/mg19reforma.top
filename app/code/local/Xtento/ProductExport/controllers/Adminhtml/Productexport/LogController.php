<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-06-22T14:36:31+02:00
 * File:          app/code/local/Xtento/ProductExport/controllers/Adminhtml/Productexport/LogController.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Adminhtml_ProductExport_LogController extends Xtento_ProductExport_Controller_Abstract
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function downloadAction()
    {
        $id = (int)$this->getRequest()->getParam('id');

        $exportedFiles = $this->_getFilesForLogId($id);
        if (!$exportedFiles) {
            return $this->_redirectReferer();
        }

        return $this->_prepareFileDownload($exportedFiles);
    }

    public function massDownloadAction()
    {
        $ids = $this->getRequest()->getParam('log');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Please select log entries to download.'));
            return $this->_redirect('*/*/');
        }

        $allExportedFiles = array();
        try {
            foreach ($ids as $id) {
                $exportedFiles = $this->_getFilesForLogId($id, true);
                if (empty($exportedFiles)) {
                    continue;
                }
                foreach ($exportedFiles as $filename => $content) {
                    if (isset($allExportedFiles[$filename])) {
                        $filename = 'duplicate_filename_' . $id . '_' . $filename;
                    }
                    $allExportedFiles[$filename] = $content;
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return $this->_redirectReferer();
        }

        return $this->_prepareFileDownload($allExportedFiles);
    }

    private function _getFilesForLogId($logId, $massDownload = false)
    {
        $model = Mage::getModel('xtento_productexport/log');
        $model->load($logId);

        if (!$model->getId()) {
            if (!$massDownload) Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('This log entry (ID: %d) does not exist anymore.', $logId));
            return false;
        }

        $filesNotFound = 0;
        $exportedFiles = array();
        $savedFiles = $model->getFiles();
        if (empty($savedFiles)) {
            if (!$massDownload) Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_productexport')->__('There is nothing to download. No files have been saved with this export. (Log ID: %d)', $logId));
            return false;
        }
        $savedFiles = explode("|", $savedFiles);

        $baseFilenames = array();
        foreach ($savedFiles as $filePath) {
            array_push($baseFilenames, basename($filePath));
        }
        $baseFilenames = array_unique($baseFilenames);

        foreach ($baseFilenames as $filename) {
            $filePath = Mage::helper('xtento_productexport/export')->getExportBkpDir() . $logId . '_' . $filename;
            $data = @file_get_contents($filePath);
            if ($data === FALSE && !$this->getRequest()->getParam('force', false)) {
                $filesNotFound++;
                if (!$massDownload) Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_productexport')->__('File not found in local backup directory: %s (Log ID: %d)', $filePath, $logId));
                if ($filesNotFound == count($baseFilenames)) {
                    return false;
                }
            }
            $exportedFiles[$filename] = $data;
        }
        if ($filesNotFound > 0 && $filesNotFound !== count($baseFilenames) && !$this->getRequest()->getParam('force', false)) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_productexport')->__('One or more files of this export have been deleted from the local backup directory. Please click <a href="%s">here</a> to download the remaining existing files. (Log ID: %d)', Mage::helper('adminhtml')->getUrl('*/*/*', array('id' => $logId, 'force' => true)), $logId));
            return false;
        }

        return $exportedFiles;
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('xtento_productexport/log');
        $model->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('This log entry does not exist anymore.'));
            return $this->_redirectReferer();
        }

        try {
            $this->_deleteFilesFromFilesystem($model);
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_productexport')->__('Log entry has been successfully deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return $this->_redirectReferer();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('log');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Please select log entries to delete.'));
            return $this->_redirect('*/*/');
        }

        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('xtento_productexport/log');
                $model->load($id);
                $this->_deleteFilesFromFilesystem($model);
                $model->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($ids)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }

    private function _deleteFilesFromFilesystem($model)
    {
        $savedFiles = $model->getFiles();
        if (empty($savedFiles)) {
            return false;
        }
        $savedFiles = explode("|", $savedFiles);

        $baseFilenames = array();
        foreach ($savedFiles as $filePath) {
            array_push($baseFilenames, basename($filePath));
        }
        $baseFilenames = array_unique($baseFilenames);

        foreach ($baseFilenames as $filename) {
            $filePath = Mage::helper('xtento_productexport/export')->getExportBkpDir() . $model->getId() . '_' . $filename;
            @unlink($filePath);
        }
        return true;
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/productexport')
            ->_title(Mage::helper('xtento_productexport')->__('Product Export'))->_title(Mage::helper('xtento_productexport')->__('Execution Log'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/productexport/log');
    }
}