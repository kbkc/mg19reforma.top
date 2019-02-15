<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2016-04-15T21:34:26+02:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Edit/Tab/Manual.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Edit_Tab_Manual extends Xtento_ProductExport_Block_Adminhtml_Widget_Tab
{
    protected function _prepareForm()
    {
        $model = Mage::registry('product_export_profile');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('manual_fieldset', array(
            'legend' => Mage::helper('xtento_productexport')->__('Manual Export Settings'),
            'class' => 'fieldset-wide',
        ));

        $fieldset->addField('manual_export_enabled', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Manual Export enabled'),
            'name' => 'manual_export_enabled',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_productexport')->__('If set to "No", this profile won\'t show up for manual exports at Catalog > Product Export > Manual Export and in the Products grid.')
        ));

        $fieldset->addField('save_files_manual_export', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Save files on destinations for manual exports'),
            'name' => 'save_files_manual_export',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_productexport')->__('Do you want to save exported files on the configured export destinations when exporting manually? Or do you just want them to be saved on the configured export destinations for automatic exports?')
        ));

        $fieldset->addField('start_download_manual_export', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Serve files to browser after exporting manually'),
            'name' => 'start_download_manual_export',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('xtento_productexport')->__('When exporting manually from the grid or "Manual Export" screen, if set to "Yes", the exported file will be served to the browser automatically after exporting. Ultimately this is controlled whether you check the "Serve file to browser after exporting" checkbox on the manual export screen or not.')
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}