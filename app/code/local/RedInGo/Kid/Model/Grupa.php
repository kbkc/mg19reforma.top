<?php

class RedInGo_Kid_Model_Grupa extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("kid/grupa");
    }
    
    public function getSelekt(){
        $tmp = array('p1');
        $tmp_wher = array();
        for ($i = 2; $i <= 10; $i++) {
            if( $grupa_array['p'.$i] ){
                $pola[] = 'p'.$i." as ".Mage::getStoreConfig('kid/pole/p'.$i);
                $tmp[] = 'p'.$i;
                $tmp_wher[] = 'p'.$i.' != \'\' ';
            };
        };
        
        $tmp_ = implode(',', $tmp);
        $sku = implode(' ," ",', $tmp);
        $and = "AND";
        if( count($tmp_wher) < 1){
            $and = "";
        };
        $tmp_wher = implode(' AND ', $tmp_wher);
        $query = "SELECT 
                GROUP_CONCAT(DISTINCT ".$sku.") as 'sku',
                GROUP_CONCAT(DISTINCT ".$sku.") as 'name',
                'grouped' as 'type',
                '".$grupa_array['category_ids']."' as 'category_ids',
                '".$grupa_array['websites']."' as 'websites', ";
        
        $query .=implode(',',$pola);
     //   if( count($tmp_wher) > 1){
            $query .= " , ";
     //   };        
        $query .= " GROUP_CONCAT( `sku`) as 'grouped_skus'
                FROM `".$resource->getTableName('redingo_kid')."`
                WHERE 
                ".$tmp_wher." ".$and." p1 = '".$grupa_array['gropa']."'
                GROUP BY ".$tmp_."
                HAVING count( * ) >1";
        return $query;
    }

    public function getPola(){
        $id = $this->getId();
        $model = Mage::getModel("kid/grupa")->load($id);
        $tmp = array();
        for ($i = 1; $i < 11; $i++) {
            if( $model->getData('c_p'.$i) ){
                $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(4, Mage::getStoreConfig('kid/pole/p'.$i) );
                $tmp[] =  $attributeModel->getData('attribute_code' ) ;
            }
        };
        return $tmp;
    }

    public function conf(){
         "<pre>";
        require_once("lib/magmi/inc/magmi_defs.php");
        require_once("lib/magmi/integration/inc/magmi_datapump.php");
        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("Default","create");
        
        $grupa_array = Mage::getModel('kid/grupa')->load( $this->getData('id') )->getData();

        $tmp = array('p1');
        $tmp_wher = array();
        for ($i = 2; $i <= 10; $i++) {
            if( $grupa_array['p'.$i] ){
                $pola[] = 'p'.$i." as ".Mage::getStoreConfig('kid/pole/p'.$i);
                $tmp[] = 'p'.$i;
                $tmp_wher[] = 'p'.$i.' != \'\' ';
            };
            if( $grupa_array['c_p'.$i] ){
                $conf[] = Mage::getStoreConfig('kid/pole/p'.$i);
            };
        };
        
	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
        
        $conf = implode(',', $conf);
        $tmp_ = implode(',', $tmp);
        $sku = implode(' ," ",', $tmp);
        $and = "AND";
        if( count($tmp_wher) < 1){
            $and = "";
        };
        $tmp_wher = implode(' AND ', $tmp_wher);
        $query = "SELECT 
                GROUP_CONCAT(DISTINCT ".$sku.") as 'sku',
                GROUP_CONCAT(DISTINCT ".$sku.") as 'name',
                'configurable' as 'type',
                'Papilart' as 'attribute_set',
                price,
                '".$grupa_array['id']."' as 'grupa',
                '2' as 'tax_class_id',
                '".$conf."' as 'configurable_attributes',
                '".$grupa_array['websites']."' as 'websites', ";
        
        //$query .=implode(',',$pola);
       // if( count($tmp_wher) > 1){
       //     $query .= " , ";
     //   };        
        $query .= " GROUP_CONCAT( `sku`) as 'simples_skus'
                FROM `".$resource->getTableName('redingo_kid')."`
                WHERE 
                ".$tmp_wher." ".$and." p1 = '".$grupa_array['gropa']."'
                GROUP BY ".$tmp_."
                HAVING count( * ) >1";

 $query;

       $results = $readConnection->fetchAll($query);
//	print_r($results);
    	foreach( $results as $value) {
            $dp->ingest( $value );
    	}

        $dp->endImportSession();
        return $tmp;
    }
    public function generoj(){
         "<pre>";
        require_once("lib/magmi/inc/magmi_defs.php");
        require_once("lib/magmi/integration/inc/magmi_datapump.php");
        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("Default","create");
        
        $grupa_array = Mage::getModel('kid/grupa')->load( $this->getData('id') )->getData();

        $tmp = array('p1');
        $tmp_wher = array();
        for ($i = 2; $i <= 10; $i++) {
            if( $grupa_array['p'.$i] ){
                $pola[] = 'p'.$i." as ".Mage::getStoreConfig('kid/pole/p'.$i);
                $tmp[] = 'p'.$i;
                $tmp_wher[] = 'p'.$i.' != \'\' ';
            };
        };
        
	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
        
        $tmp_ = implode(',', $tmp);
        $sku = implode(' ," ",', $tmp);
        $and = "AND";
        if( count($tmp_wher) < 1){
            $and = "";
        };
        $tmp_wher = implode(' AND ', $tmp_wher);
         $query = "SELECT 
                GROUP_CONCAT(DISTINCT ".$sku.") as 'sku',
                GROUP_CONCAT(DISTINCT ".$sku.") as 'name',
                'grouped' as 'type',
                'Papilart' as 'attribute_set',
                '".$grupa_array['websites']."' as 'websites', ";
        
      //  $query .=implode(',',$pola);
     //   if( count($tmp_wher) > 1){
         //   $query .= " , ";
    //    };        
        $query .= " GROUP_CONCAT( `sku`) as 'grouped_skus'
                FROM `".$resource->getTableName('redingo_kid')."`
                WHERE 
                ".$tmp_wher." ".$and." p1 = '".$grupa_array['gropa']."'
                GROUP BY ".$tmp_."
                HAVING count( * ) >1";
        
        
//         echo $query;
//        exit();
       $results = $readConnection->fetchAll($query);
//	print_r($results);
    	foreach( $results as $value) {
            $dp->ingest( $value );
    	}
        $dp->endImportSession();
        return $tmp;
    }
}