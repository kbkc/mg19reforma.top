<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:06:04+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Destination/Edit/Tabs.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Destination_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('destination_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('xtento_productexport')->__('Export Destination'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label' => Mage::helper('xtento_productexport')->__('Destination Configuration'),
            'title' => Mage::helper('xtento_productexport')->__('Destination Configuration'),
            'content' => $this->getLayout()->createBlock('xtento_productexport/adminhtml_destination_edit_tab_configuration')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}