<?php
class RedInGo_Kid_Model_Select_Rodzaj
{
  public function toOptionArray()  {
    $rodzaj = Mage::getModel('kid/rodzaj')->getResourceCollection()
            ->setOrder('nazwa','ASC')
            ->getData();

    $source =array();
    $source[] = array('value' => "", 'label' =>"");
    foreach ($rodzaj as $item) {
        $source[] = array('value' => $item['nazwa'] , 'label' => $item['nazwa']);
    } 
    return $source;
  }
}

