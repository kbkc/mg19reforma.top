<?php
class RedInGo_Kid_Model_Select_Cena
{
  public function toOptionArray()
  {

    $source[1] = array('value' => "n", 'label' => "Cena Netto");
    $source[2] = array('value' => "b", 'label' => "Cena Brutto");

    return $source;
  }
}
