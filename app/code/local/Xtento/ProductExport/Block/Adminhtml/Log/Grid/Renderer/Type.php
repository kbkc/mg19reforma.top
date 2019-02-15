<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-03-07T13:13:07+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Log/Grid/Renderer/Type.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Log_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    public function render(Varien_Object $row)
    {
        if ($row->getExportType() != Xtento_ProductExport_Model_Export::EXPORT_TYPE_EVENT) {
            return parent::render($row);
        } else {
            return parent::render($row)." (".Mage::helper('xtento_productexport')->__('Event').": ".$row->getExportEvent().")";
        }
    }
}