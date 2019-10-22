<?php

class RedInGo_Kid_Model_Kid extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("kid/kid");

    }
    public function getToSku( $sku ){
        $collection = Mage::getModel('kid/kid')
                    ->getCollection()
                    ->addFieldToFilter('sku', $tag);
        return $collection->getFirstItem();
    }
    
    public function getToWfmag( $sku ){
        $collection = Mage::getModel('kid/kid')
                    ->getCollection()
                    ->addFieldToFilter('wfmag', $tag);
        return $collection->getFirstItem();
    }
}
	 