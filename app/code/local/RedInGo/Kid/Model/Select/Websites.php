<?php
class RedInGo_Kid_Model_Select_Websites
{
  public function toOptionArray()  {
    $_websites = Mage::app()->getWebsites(); 
    $source =array();
    $source[] = array('value' => "", 'label' =>"");
    
    foreach ($_websites as $website) {
        $source[] = array('value' => $website->getData('code') , 'label' => $website->getData('code') );
    } 
    return $source;
  }
}

