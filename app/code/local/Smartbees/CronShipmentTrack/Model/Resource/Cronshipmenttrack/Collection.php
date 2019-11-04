<?php

class Smartbees_CronShipmentTrack_Model_Resource_Cronshipmenttrack_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cronshipmenttrack/cronshipmenttrack');
    }
}