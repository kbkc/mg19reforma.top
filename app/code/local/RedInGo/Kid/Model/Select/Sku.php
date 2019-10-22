<?php
class RedInGo_Kid_Model_Select_Sku
{
  public function toOptionArray()
  {

    $source[0] = array('value' => "kat", 'label' => "Indeks katalogowy");
    $source[1] = array('value' => "id", 'label' => "id");

    return $source;
  }
}
