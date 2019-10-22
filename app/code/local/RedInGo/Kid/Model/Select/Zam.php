<?php
class RedInGo_Kid_Model_Select_Zam
{
  public function toOptionArray()  {
	$options = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
	$source =array();
	foreach ($options as $item) {
		$source[] = array('value' => $item["status"] , 'label' => $item["label"] );
	}
    return $source;
  }
}
