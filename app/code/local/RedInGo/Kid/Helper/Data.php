<?php
class RedInGo_Kid_Helper_Data extends Mage_Core_Helper_Abstract {
    private $dir_xls = DS.'app'.DS.'code'.DS.'local'.DS.'RedInGo'.DS.'Kid'.DS.'xsd'.DS;
    public function __get( $name )
    {
      if( $name == 'dir_xls'){
        return Mage::getBaseDir().$this->dir_xls;
      }
      return NULL;
    }
    public function magmi(){
        require_once("lib/magmi/inc/magmi_defs.php");
        require_once("lib/magmi/integration/inc/magmi_datapump.php");
        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("Default","create");
        return $dp;
    }

    public function kat(){
        $collection = Mage::getModel('catalog/category')->getCollection();
        $tmp = array();
        foreach ($collection as $value) {
            $id = (int)$value->getId();
            $kat = Mage::getModel('catalog/category')->load( $id );
            $wfmag = $kat->getData('wfmag');
            if( $wfmag ){
                $kat = Mage::getModel('catalog/category')->load( $id );
                $tmp[ $kat->getData('wfmag') ] = $id;
            }
        }
        return $tmp;
    }
    // public function wfmag($id){
    //     return Mage::getModel('kid/kid')->getCollection()
    //             ->addFieldToFilter('wfmag',  $id )
    //             ->getFirstItem()->getSku();
    // }

    public function sku($id){
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT * FROM ' . $resource->getTableName('kid/kid').' '
                . ' WHERE wfmag = '. (int)$id;
        $results = $readConnection->fetchAll($query);
        unset($resource);
        unset($readConnection);
        flush();
        return $results[0]['sku'];
    }

}
