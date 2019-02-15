<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-07-25T17:29:48+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/System/Config/Source/Export/Profile.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_System_Config_Source_Export_Profile
{
    public function toOptionArray($all = false, $entity = false, $getLastExportedId = false)
    {
        $profileCollection = Mage::getModel('xtento_productexport/profile')->getCollection();
        if (!$all) {
            $profileCollection->addFieldToFilter('enabled', 1);
            $profileCollection->addFieldToFilter('manual_export_enabled', 1);
        }
        if ($entity) {
            $profileCollection->addFieldToFilter('entity', $entity);
        }
        $profileCollection->getSelect()->order('entity ASC');
        $returnArray = array();
        foreach ($profileCollection as $profile) {
            $returnArray[] = array(
                'profile' => $profile,
                'value' => $profile->getId(),
                'label' => $profile->getName(),
                'entity' => $profile->getEntity()
            );
        }
        if (empty($returnArray)) {
            $returnArray[] = array(
                'profile' => new Varien_Object(),
                'value' => '',
                'label' => Mage::helper('xtento_productexport')->__('No profiles available. Add and enable export profiles for the %s entity first.', $entity),
                'entity' => ''
            );
        }
        return $returnArray;
    }
}
