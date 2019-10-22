<?php
class RedInGo_Kid_Model_Select_Reindex
{
  public function toOptionArray()  {
    $collection = Mage::getResourceModel('index/process_collection');
    foreach ($collection as $item) {
      $source[] = array('value' => $item->getId() , 'label' => $item->getIndexer()->getName() );
    }
    return $source;
  }
}
