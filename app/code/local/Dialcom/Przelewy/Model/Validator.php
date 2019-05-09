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
require_once Mage::getBaseDir('code').'/'.Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool').'/Dialcom/Przelewy/class_przelewy24.php';
class Dialcom_Przelewy_Model_Validator extends Mage_Core_Model_Config_Data {
    private $extracharge;
    public function save() {
        $path=$this->getPath();
        $field=substr($path, strrpos($path, '/')+1);
        if ($field=='merchant_id') {
            $val=(int)$this->getValue();
            if ($val < 1000)
                Mage::throwException( 'Przelewy24: '.Mage::helper('przelewy')->__('Incorrect seller ID'));
        } elseif ($field=='shop_id') {
            $val=(int)$this->getValue();
            if ($val < 1000)
                Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('Incorrect shop ID'));
        } elseif ($field=='salt') {
            $value=$this->getValue();
            if (strlen($value)!=16 || !ctype_xdigit($value))
                Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('The CRC key must have 16 characters'));
        } elseif ($field=='mode') {
            $settings=$this->getFieldsetData();
            $P24=new Przelewy24Class($settings['merchant_id'], $settings['shop_id'], $settings['salt'], ($settings['mode']==1));
            $ret=$P24->testConnection();
            if ($ret['error']!=0) {
                Mage::throwException('Przelewy24: '.Mage::helper('przelewy')->__('Bad Shop ID, Seller or CRC Key for this plug-in mode'));
            }
        }
        parent::save();
    }
}
