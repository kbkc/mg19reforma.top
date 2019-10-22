<?php
class RedInGo_Kid_Model_Select_Pole
{
  public function toOptionArray()
  {
      
    $source[0] = array('value' => "", 'label' => "");
    $source[1] = array('value' => "grouped", 'label' => "grouped");
    $source[2] = array('value' => "configurable", 'label' => "configurable");

    return $source;
  }
}
