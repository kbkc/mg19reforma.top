<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Validator
 *
 * @author adamm
 */
require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/class_przelewy24.php';

/**
 * Class Dialcom_Przelewy_Model_Advancedvalidator
 */
class Dialcom_Przelewy_Model_Advancedvalidator extends Mage_Core_Model_Config_Data
{
    private $extracharge;

    /** @var int */
    private $storeID;

    public function save()
    {
        $path = $this->getPath();
        $field = substr($path, strrpos($path, '/') + 1);
        $this->storeID = Mage::helper("przelewy")->getStoreID();
        if ($field == 'oneclick') {
            if (!!$this->getValue()) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('przelewy')->__('Oneclick payments also require the account configuration at Przelewy24.pl, for this purpose please contact us at partner@przelewy24.pl'));
            }
        } elseif ($field == 'api_key') {
            $value = $this->getValue();
            if (!empty($value)) {
                if (strlen($value) != 32 || !ctype_xdigit($value)) {
                    Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('The API key should have 32 characters'));
                } else {
                    if (!extension_loaded('soap')) {
                        Mage::throwException('No soap extension');
                    } else {

                        $P24C = new Przelewy24Class(
                            Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID),
                            Mage::getStoreConfig('payment/dialcom_przelewy/shop_id', $this->storeID),
                            Mage::getStoreConfig('payment/dialcom_przelewy/salt', $this->storeID),
                            Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID) == '1'
                        );

                        try {
                            $s = new \SoapClient($P24C->getHost() . 'external/wsdl/service.php?wsdl',
                                array('trace' => true, 'exceptions' => true));
                            $ret = $s->TestAccess(Mage::getStoreConfig('payment/dialcom_przelewy/shop_id',
                                $this->storeID), $value);
                        } catch (Exception $e) {
                            error_log(__METHOD__ . ' ' . $e->getMessage());
                        }
                        if (!$ret) {
                            Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('Incorrect API key'));
                        }
                    }
                }
            }
        } elseif ($field == 'ga_key') {
            $value = $this->getValue();
            if (!empty($value) && !preg_match('#^[A-Z]{2}\-\d+\-\d+$#', $value)) {
                Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('Bad Google Analytics key format. Permitted format:').' UA-0123456-7');
            }
        } elseif ($field == 'timelimit') {
            $value = (int)$this->getValue();
            if ($value < 0 || $value > 99) {
                Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('Incorrect time limit for completing the transaction. Valid values: from 0 to 99'));
            }
        } elseif ($field == 'extracharge_amount') {
            $value = strtr($this->getValue(), ',', '.');
            if (!empty($value) && !is_numeric($value)) {
                Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('Incorrect amount for calculating additional payment'));
            }
            $this->setValue($value);
        } elseif ($field == 'extracharge_percent') {
            $value = strtr($this->getValue(), ',', '.');
            if (!empty($value) && !is_numeric($value)) {
                Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('Incorrect percentage of the amount for an additional payment'));
            }
            $this->setValue($value);
        } elseif ($field == 'zencard') {
            $merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID);
            $apiKey = Mage::getStoreConfig('przelewytab1/klucze/api_key', $this->storeID);
            $value = $this->getValue();
            if (!empty($value) && $value == '1') {
                if (!extension_loaded('openssl')) {
                    Mage::throwException(Mage::helper('przelewy')->__('No openssl extension'));
                }
                if (!extension_loaded('curl')) {
                    Mage::throwException(Mage::helper('przelewy')->__('No curl extension'));
                }
                $zenCardApi = new ZenCardApi($merchantId, $apiKey);
                if (!$zenCardApi->isEnabled()) {
                    Mage::throwException(Mage::helper('przelewy')->__('No extension openssl. ZenCard functionality is not available for the account: ') . $merchantId);
                }
            }
            $this->setValue($value);
        }
        parent::save();
    }
}
