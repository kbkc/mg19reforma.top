<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-07-01T23:20:39+02:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Destination/Edit/Tab/Type/Custom.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Destination_Edit_Tab_Type_Custom
{
    // Custom Type Configuration
    public function getFields($form)
    {
        $fieldset = $form->addFieldset('config_fieldset', array(
            'legend' => Mage::helper('xtento_productexport')->__('Custom Type Configuration'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('custom_class', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Custom Class Identifier'),
            'name' => 'custom_class',
            'note' => Mage::helper('xtento_productexport')->__('You can set up an own class in our (or another) module which gets called when exporting. The saveFiles($fileArray ($filename => $contents)) function would be called in your class. If your class was called Xtento_ProductExport_Model_Destination_Myclass then the identifier to enter here would be xtento_productexport/destination_myclass'),
            'required' => true
        ));
    }
}