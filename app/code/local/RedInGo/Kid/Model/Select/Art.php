<?php
class RedInGo_Kid_Model_Select_Art
{
  public function toOptionArray( $typ )
  {
    $atr = Mage::getResourceModel('eav/entity_attribute_collection')
                    ->setEntityTypeFilter(4)
                    //->addFieldToFilter('backend_type', 'varchar')
                    ->addSetInfo()
                    ->getData();
                    echo $typ;
// echo "<pre>";textarea
//                     print_r( $atr );
    $source =array();
    $source[] = array('value' => "", 'label' =>"");
    foreach ($atr as $item) {
      if( $item['frontend_label'] )
      {
        $source[] = array('value' => $item['attribute_code'], 'label' =>$item['frontend_label']);
      }
    }
    return $source;
  }
}
