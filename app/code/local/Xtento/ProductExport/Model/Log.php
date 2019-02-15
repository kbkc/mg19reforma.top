<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:06:04+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Log.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Log extends Mage_Core_Model_Abstract
{
    /*
     * Log model which keeps track of successful/failed export attempts
     */
    protected $_resultMessages = array();

    // Log result types
    const RESULT_NORESULT = 0;
    const RESULT_SUCCESSFUL = 1;
    const RESULT_WARNING = 2;
    const RESULT_FAILED = 3;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('xtento_productexport/log');
    }

    public function setResult($resultLevel)
    {
        if ($this->getResult() === NULL) {
            $this->setData('result', $resultLevel);
        } else if ($resultLevel > $this->getResult()) { // If result is failed, do not reset to warning for example.
            $this->setData('result', $resultLevel);
        }
    }

    public function addResultMessage($message)
    {
        array_push($this->_resultMessages, $message);
    }

    public function getResultMessages()
    {
        if (empty($this->_resultMessages)) {
            return false;
        }
        return (count($this->_resultMessages) > 1) ? implode("\n", $this->_resultMessages) : $this->_resultMessages[0];
    }
}