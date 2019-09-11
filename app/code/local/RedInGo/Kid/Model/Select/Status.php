<?php
class RedInGo_Kid_Model_Select_Status
{
  const STATUS_ENABLED    = 1;
  const STATUS_DISABLED   = 2;
  public function toOptionArray()
  {
    return array(
        self::STATUS_ENABLED    => Mage::helper('catalog')->__('Enabled'),
        self::STATUS_DISABLED   => Mage::helper('catalog')->__('Disabled')
    );
  }
}
