<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:06:03+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/System/Config/Source/Export/Events.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_System_Config_Source_Export_Events
{
    public function toOptionArray($entity = false)
    {
        $optionArray = array();
        $events = Mage::getSingleton('xtento_productexport/observer_event')->getEvents($entity);
        foreach ($events as $entityEvents) {
            foreach ($entityEvents as $eventId => $eventOptions) {
                $optionArray[$eventId] = $eventOptions['label'];
            }
        }
        return $optionArray;
    }
}