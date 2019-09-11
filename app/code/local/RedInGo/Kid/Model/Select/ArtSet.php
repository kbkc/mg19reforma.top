<?php
class RedInGo_Kid_Model_Select_ArtSet
{
  public function toOptionArray()  {
        $atrSet = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter(4);
		
        $source =array();
        $source[] = array('value' => "", 'label' =>"");
        foreach ($atrSet as $item) {
                $entityTypeId = $item->getId();
                $name = $item->getAttributeSetName();
                $source[] = array('value' => $entityTypeId , 'label' => $name);
        } 
        return $source;
  }
}

