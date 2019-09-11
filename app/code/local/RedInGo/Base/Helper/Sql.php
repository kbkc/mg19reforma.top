<?php
class RedInGo_Base_Helper_Sql extends Mage_Core_Helper_Abstract
{
    public function insert( $tab, $pola, $updat = NULL){
	$resource = Mage::getSingleton('core/resource');
	$writeConnection = $resource->getConnection('core_write');
	$table = $resource->getTableName( $tab );
	
        $tmp1 = array(); 
        $tmp2 = array();
        foreach ($pola as $key => $value) {
                $tmp1[] ='`'.$key.'`';
                $tmp2[] ='\''.$value.'\'';
        }
        $tmp1 = implode(',', $tmp1);
        $tmp2 = implode(',', $tmp2);
        $query =   "INSERT INTO `".$table."` ( ".$tmp1." ) VALUES ( ".$tmp2." )";

        if( is_array($updat) ){
            $query .= "ON DUPLICATE KEY UPDATE ";
            $tmp3 = array();
            foreach ($updat as $key => $value) {
                $tmp3[] = $key.'=\''.$value.'\'';
            }
            $tmp3 = implode(',', $tmp3);
            $query .= $tmp3;
        }
        $query .= ';';
        try {
            $writeConnection->query($query);
            return $query;
        } catch (Exception $exc) {
            return $query;
        }
    }
    public function selekt( $tab, $pola =  array('*'), $updat = NULL){
        
	return "SELECT "
        . "".implode(',', $pola).""
        . " FROM `".$tab."`";
    }
}
	 