<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:06:03+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Tools.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Tools extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('xtento/productexport/tools.phtml');
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_productexport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}