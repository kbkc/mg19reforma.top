<?php
class RedInGo_Kid_Model_Mysql4_Status extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("kid/status", "id");
    }
}