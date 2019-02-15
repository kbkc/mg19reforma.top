<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:04:46+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Destination/Interface.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

interface Xtento_ProductExport_Model_Destination_Interface
{
    public function testConnection();
    public function saveFiles($fileArray);
}