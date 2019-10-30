<?php

class Smartbees_CronShipmentTrack_Model_Resource_Cronshipmenttrack extends Mage_Core_Model_Resource_Db_Abstract
{
    //public function _construct()
    public function _construct()
    {
        //parent::_construct();
        $this->_init('cronshipmenttrack/cronshipmenttrack', 'id');
    }
}