<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:07:18+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Log.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'xtento_productexport';
        $this->_controller = 'adminhtml_log';
        $this->_headerText = Mage::helper('xtento_productexport')->__('Product Export - Execution Log');
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_productexport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}