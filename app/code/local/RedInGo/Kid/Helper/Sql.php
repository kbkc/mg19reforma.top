<?php

class RedInGo_Kid_Helper_Sql extends Mage_Core_Helper_Abstract{

    public function off(){
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        
        $tab2 = $resource->getTableName('catalog_product_entity_int');
        $id_status = $this->getAttributeId(4, 'status');

        $query = "UPDATE $tab2 SET value = 2 WHERE attribute_id = $id_status";
        $writeConnection->query($query);  
    }
    
    public function getAttributeId($type, $code){
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $tab = $resource->getTableName('eav_attribute');
        $query = "select attribute_id from $tab where entity_type_id = $type and attribute_code = '$code'";
        $results = $readConnection->fetchAll($query);
        return $id_status = (int)$results[0]['attribute_id'];
    }
    
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