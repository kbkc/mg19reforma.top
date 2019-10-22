<?php
class RedInGo_Kid_Model_Select_Kat
{
  public function toOptionArray()
  {
        $kat = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('*');

        $source =array();
        $source[] = array('value' => "", 'label' =>"");
	foreach ($kat as $item) {
		$source[] = array('value' => $item->getId(), 'label' =>$item->getName() );
	} 
	
    return $source;
  }
}

