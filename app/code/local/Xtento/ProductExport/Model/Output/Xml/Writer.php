<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2016-10-18T22:23:49+02:00
 * File:          app/code/local/Xtento/ProductExport/Model/Output/Xml/Writer.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Output_Xml_Writer extends XMLWriter
{
    protected $_escapeSpecialChars = false;
    protected $_disableEscapingFields = array();

    public function __construct()
    {
        $this->openMemory();
        $this->setIndent(2);
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('objects');
    }

    public function setEscapeSpecialChars($escapeSpecialChars)
    {
        $this->_escapeSpecialChars = $escapeSpecialChars;
    }

    public function setDisableEscapingFields($disableEscapingFields)
    {
        $this->_disableEscapingFields = $disableEscapingFields;
    }

    public function setElement($elementName, $elementText)
    {
        $elementName = trim($elementName);
        if (isset($elementName[0]) && is_numeric($elementName[0])) {
            $elementName = '_' . $elementName;
        }
        $this->startElement($elementName);
        $this->text($elementText);
        $this->endElement();
    }

    public function fromArray($array, $parentKey = '')
    {
        if (is_array($array)) {
            foreach ($array as $key => $element) {
                if (is_array($element)) {
                    $key = $this->handleSpecialParentKeys($key, $parentKey);
                    $this->startElement($key);
                    $this->fromArray($element, $key);
                    $this->endElement();
                } else if (is_string($key)) {
                    $this->setElement($key, $this->_stripInvalidXml($key, $element));
                }
            }
        }
    }

    public function getDocument()
    {
        $this->endElement();
        $this->endDocument();
        return $this->outputMemory();
    }

    public function handleSpecialParentKeys($key, $parentKey)
    {
        if (is_numeric($key) && $parentKey == '') {
            $key = 'object';
        }
        $iteratingKeys = Xtento_ProductExport_Model_Output_Abstract::$iteratingKeys;
        if (is_numeric($key) && $parentKey !== '') {
            if (in_array($parentKey, $iteratingKeys) || isset($iteratingKeys[$parentKey])) {
                if (isset($iteratingKeys[$parentKey])) {
                    $key = $iteratingKeys[$parentKey];
                } else {
                    $key = substr($parentKey, 0, -1);
                }
            }
            // Ensure a valid string key - thanks to Thomas Hägi
            if (is_numeric($key)) {
                // Create pseudo-singular key from parent key if possible
                $len = strlen($parentKey);
                if ($parentKey && $parentKey[$len - 1] == 's') {
                    $key = substr($parentKey, 0, $len - 1);
                } else {
                    $key = 'object';
                }
            }
        }
        return $key;
    }

    private function _stripInvalidXml($key, $string)
    {
        $strippedValue = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $string);
        if ($this->_escapeSpecialChars) {
            if (!in_array($key, $this->_disableEscapingFields)) {
                #$strippedValue = htmlspecialchars($strippedValue);
            }
        }
        return $strippedValue;
    }
}