<?php
class RedInGo_Kid_Model_Select_Tax
{
  public function toOptionArray()
  {
	$taxs = $productTaxClass = Mage::getModel('tax/class')->getCollection();
	$source =array();
	foreach ($taxs as $item) {
		if($item->getData('class_type') == "PRODUCT" ){
			$source[] = array('value' => $item->getData('class_id'), 'label' =>$item->getData('class_name'));
		};
	} 
	
    return $source;
  }
}
