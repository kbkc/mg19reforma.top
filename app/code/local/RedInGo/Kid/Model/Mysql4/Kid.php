<?php
class RedInGo_Kid_Model_Mysql4_Kid extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("kid/kid", "kid_id");
    }
}