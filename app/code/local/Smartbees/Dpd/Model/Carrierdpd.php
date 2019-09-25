<?php

class Smartbees_Dpd_Model_Carrierdpd
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'smartbees';
 
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    $result = Mage::getModel('shipping/rate_result');
    $result->append($this->_getDefaultRate());
    $result->append($this->_getExpressShippingRate());
    return $result;
  }
 
  public function getAllowedMethods()
  {
    return array(
      'smartbees' => $this->getConfigData('name'),
    );
  }
 
  protected function _getDefaultRate()
  {
    $rate = Mage::getModel('shipping/rate_result_method');
     
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod($this->_code);
    $rate->setMethodTitle($this->getConfigData('title'));
    $rate->setPrice(Mage::getStoreConfig('carriers/smartbees/price'));
    $rate->setCost(0);
    return $rate;
  }
  protected function _getExpressShippingRate()
  {
      $rate = Mage::getModel('shipping/rate_result_method');
      /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
      $rate->setCarrier($this->_code);
      $rate->setCarrierTitle($this->getConfigData('title'));
      $rate->setMethod('express');
      $rate->setMethodTitle($this->getConfigData('titlefordelivery'));
      $rate->setPrice(Mage::getStoreConfig('carriers/smartbees/cenapobranie'));
      $rate->setCost(0);
      return $rate;
  }
}