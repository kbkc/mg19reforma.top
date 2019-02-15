<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:06:04+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/System/Config/Backend/Export/Enabled.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_System_Config_Backend_Export_Enabled extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        Mage::register('productexport_modify_event', true, true);
        parent::_beforeSave();
    }

    public function has_value_for_configuration_changed($observer)
    {
        if (Mage::registry('productexport_modify_event') == true) {
            Mage::unregister('productexport_modify_event');
            Xtento_ProductExport_Model_System_Config_Source_Order_Status::isEnabled();
        }
    }
}
