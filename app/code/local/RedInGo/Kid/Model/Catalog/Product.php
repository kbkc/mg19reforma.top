<?php
class RedInGo_Kid_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    public function wfmag(){
        $id = Mage::getModel('kid/kid')->getCollection()
                ->addFieldToFilter('sku',  parent::getSku() )
                ->getFirstItem()->getWfmag();
        if( $id ){
            return $id;
        } else{
            return 0;
        }
    }
    
    public function jedn(){
        $j = Mage::getModel('kid/kid')->getCollection()
                ->addFieldToFilter('sku',  parent::getSku() )
                ->getFirstItem()->getDomJed();
        if( $j ){
            return $j;
        } else{
            return false;
        }
    }
    
    public function delete(){
        parent::delete();
        Mage::getModel('kid/kid')->getCollection()
                ->addFieldToFilter('sku',  parent::getSku() )
                ->getFirstItem()->delete();

    }
}
		