<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2016-10-17T16:43:35+02:00
 * File:          app/code/local/Xtento/ProductExport/controllers/Adminhtml/Productexport/ProfileController.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Adminhtml_ProductExport_ProfileController extends Xtento_ProductExport_Controller_Abstract
{
    public function indexAction()
    {
        if (!Xtento_ProductExport_Model_System_Config_Source_Order_Status::isEnabled() || !Mage::helper('xtento_productexport')->getModuleEnabled()) {
            return $this->_redirect('*/productexport_index/disabled');
        }
        $this->_initAction()->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('xtento_productexport/profile');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('This profile no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if (!$model->getEntity()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('No export entity has been set for this profile.'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            // Default values
            $model->setSaveFilesManualExport(1);
            $model->setSaveFilesLocalCopy(1);
        }

        $this->_title($model->getId() ? $model->getName() : Mage::helper('xtento_productexport')->__('New Profile'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        } else {
            // Handle certain fields
            $fields = array('store_ids', 'event_observers', 'export_filter_product_type', 'attributes_to_select', 'export_filter_product_visibility', 'export_filter_product_status');
            foreach ($fields as $field) {
                $value = $model->getData($field);
                if (!is_array($value)) {
                    $model->setData($field, explode(',', $value));
                }
            }
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        Mage::unregister('product_export_profile');
        Mage::register('product_export_profile', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('xtento_productexport')->__('Edit Profile') : Mage::helper('xtento_productexport')->__('New Profile'), $id ? Mage::helper('xtento_productexport')->__('Edit Profile') : Mage::helper('xtento_productexport')->__('New Profile'))
            ->_addContent($this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit')->setData('action', $this->getUrl('*/*/save')))
            ->_addLeft($this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(1);
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(1);

        $this->renderLayout();

        if (Mage::getSingleton('adminhtml/session')->getProfileDuplicated()) {
            Mage::getSingleton('adminhtml/session')->setProfileDuplicated(0);
        }
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getModel('xtento_productexport/profile');
            if (isset($postData['rule']['conditions'])) {
                $postData['conditions'] = $postData['rule']['conditions'];
                unset($postData['rule']);
            }
            $model->setData($postData);
            if ($model->getId()) {
                $profile = Mage::getModel('xtento_productexport/profile')->load($model->getId());
                Mage::unregister('product_export_profile');
                Mage::register('product_export_profile', $profile);
                try {
                    $model->loadPost($postData);
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('An error occurred while saving this export profile: ' . $e->getMessage()));
                }
            }
            $model->setLastModification(now());

            if (!$model->getId()) {
                $model->setEnabled(1);
            }

            // Handle certain fields
            $fields = array('store_ids', 'event_observers', 'export_filter_product_type', 'attributes_to_select', 'export_filter_product_visibility', 'export_filter_product_status');
            foreach ($fields as $field) {
                $value = $model->getData($field);
                $model->setData($field, '');
                if (is_array($value)) {
                    $model->setData($field, implode(',', $value));
                }
                if (empty($value)) {
                    $model->setData($field, '');
                }
            }
            // Handle date fields
            $fields = array('export_filter_datefrom', 'export_filter_dateto', 'export_filter_last_x_days', 'export_filter_updated_last_x_minutes');
            foreach ($fields as $field) {
                $value = $model->getData($field);
                if (empty($value)) {
                    $model->setData($field, NULL);
                }
            }

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_productexport')->__('The export profile has been saved.'));
                if ($this->getRequest()->getParam('continue')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'active_tab' => $this->getRequest()->getParam('active_tab')));
                } else {
                    $this->_redirect('*/*');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('An error occurred while saving this export profile: ' . $e->getMessage()));
            }

            Mage::getSingleton('adminhtml/session')->setFormData($postData);
            $this->_redirectReferer();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Could not find any data to save in the POST request. POST request too long maybe?'));
            $this->_redirect('*/*');
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('xtento_productexport/profile');
        $model->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('This profile does not exist anymore.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_productexport')->__('Profile has been successfully deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function validateXslTemplateAction()
    {
        $xslTemplate = $this->getRequest()->getPost('xsl_template', false);
        if (!$xslTemplate || empty($xslTemplate)) {
            $this->getResponse()->appendBody(Mage::helper('xtento_productexport')->__('No XSL Template supplied.'));
            return;
        }
        $exportId = $this->getRequest()->getPost('increment_id', false);
        $profileId = $this->getRequest()->getPost('profile_id', false);
        $profile = Mage::getModel('xtento_productexport/profile')->load($profileId);
        $profile->setXslTemplate($xslTemplate);
        // Export
        try {
            $outputFiles = Mage::getModel('xtento_productexport/export', array('profile' => $profile))->testExport($exportId);
            if (!is_array($outputFiles)) {
                $this->getResponse()->appendBody($outputFiles);
            } else {
                $count = 0;
                foreach ($outputFiles as $filename => $outputFile) {
                    $count++;
                    if ($count > 1) {
                        $this->getResponse()->appendBody("\n");
                    }
                    $this->getResponse()->appendBody("File: " . $filename . "\n\n" . $outputFile);
                }
                // Store file so it can be served to the browser
                if ($this->getRequest()->getParam('serve_to_browser', false)) {
                    $serializedArray = @serialize($outputFiles);
                    $tmpExportDir = Mage::getBaseDir('var') . DS . "product_export_test" . DS;
                    if (!Mage::getConfig()->createDirIfNotExists($tmpExportDir)) {
                        $this->getResponse()->appendBody(sprintf("\n\nAttention: Could not create temporary directory '%s' to store test export for serving the file to the browser.", $tmpExportDir));
                    } else {
                        if (!@file_put_contents($tmpExportDir . 'profile_' . $profileId, $serializedArray)) {
                            $this->getResponse()->appendBody(sprintf("\n\nAttention: Could not save temporary file in directory '%s' to store test export for serving the file to the browser.", $tmpExportDir));
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->getResponse()->appendBody(Mage::helper('xtento_productexport')->__('Error: %s', $e->getMessage()));
            if (preg_match('/have been exported/', $e->getMessage())) {
                $this->getResponse()->appendBody(" If the ID you tried to export exists in Magento, make sure you set up no filters at 'Filters / Actions' that stop the object from being exported.");
            }
        }
    }

    public function downloadTestExportAction()
    {
        $profileId = $this->getRequest()->getParam('profile_id', false);
        if (!$profileId) {
            $this->getResponse()->appendBody(Mage::helper('xtento_productexport')->__('No profile ID specified.'));
            return;
        }
        $tmpExportDir = Mage::getBaseDir('var') . DS . "product_export_test" . DS;
        $tmpFile = $tmpExportDir . 'profile_' . $profileId;
        if (!file_exists($tmpFile)) {
            $this->getResponse()->appendBody("File does not exist.");
            return;
        }
        $fileContents = @unserialize(file_get_contents($tmpFile));
        return $this->_prepareFileDownload($fileContents);
    }

    public function showFieldsAction()
    {
        $id = (int)$this->getRequest()->getParam('profile_id');
        $model = Mage::getModel('xtento_productexport/profile')->load($id);
        if (!$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('This profile no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        Mage::unregister('product_export_profile');
        Mage::register('product_export_profile', $model);
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('xtento_productexport/adminhtml_profile_fields'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(1);
        $this->renderLayout();
    }

    public function showFieldsXmlAction()
    {
        $id = (int)$this->getRequest()->getParam('profile_id');
        $model = Mage::getModel('xtento_productexport/profile')->load($id);
        if (!$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('This profile no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        Mage::unregister('product_export_profile');
        Mage::register('product_export_profile', $model);
        $productExport = Mage::getSingleton('xtento_productexport/export_entity_' . Mage::registry('product_export_profile')->getEntity(), array('profile' => Mage::registry('product_export_profile')));
        $productExport->setShowEmptyFields(1);
        $filterField = $model->getEntity() == Xtento_ProductExport_Model_Export::ENTITY_REVIEW ? 'main_table.review_id': 'entity_id';
        $productExport->setCollectionFilters(
            array(array($filterField => array('in' => explode(",", $this->getRequest()->getParam('test_id')))))
        );
        $returnArray = $productExport->runExport();
        $xmlData = Mage::getModel('xtento_productexport/output_xml', array('profile' => Mage::registry('product_export_profile')))->convertData($returnArray);
        if (count($xmlData) > 0) {
            $this->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'text/xml')
                ->setBody($xmlData[0]);
        }
    }

    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Please select a profile to duplicate.'));
            return $this->_redirect('*/*');
        }

        try {
            $model = Mage::getModel('xtento_productexport/profile');
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('This profile does not exist anymore.'));
                return $this->_redirect('*/*');
            }

            $profile = clone $model;
            $profile->setEnabled(0);
            $profile->setId(null);
            $profile->setLastModification(now());
            $profile->setLastExecution(null);
            $profile->save();

            Mage::getSingleton('adminhtml/session')->setProfileDuplicated(1);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_productexport')->__('The profile has been duplicated. Please make sure to enable it.'));
            $this->_redirect('*/*/edit', array('id' => $profile->getId()));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function newConditionHtmlAction()
    {
        $profileId = $this->getRequest()->getParam('profile_id');
        $profile = Mage::getModel('xtento_productexport/profile')->load($profileId);
        Mage::unregister('product_export_profile');
        Mage::register('product_export_profile', $profile);

        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('salesrule/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function destinationAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function destinationGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function logGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function historyGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('profile');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Please select profiles to delete.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('xtento_productexport/profile');
                $model->load($id);
                $model->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($ids)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function massUpdateStatusAction()
    {
        $ids = $this->getRequest()->getParam('profile');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_productexport')->__('Please select profiles to modify.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('xtento_productexport/profile');
                $model->load($id);
                $model->setEnabled($this->getRequest()->getParam('enabled'));
                $model->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully updated', count($ids)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/productexport')
            ->_title(Mage::helper('xtento_productexport')->__('Product Export'))->_title(Mage::helper('xtento_productexport')->__('Profiles'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/productexport/profile');
    }
}