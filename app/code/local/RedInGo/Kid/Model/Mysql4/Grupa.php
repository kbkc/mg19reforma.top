<?php
class RedInGo_Kid_Model_Mysql4_Grupa extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("kid/grupa", "id");
    }
}