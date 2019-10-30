<?php

class Smartbees_CronShipmentTrack_Model_Cronshipmenttrack extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cronshipmenttrack/cronshipmenttrack');
    }
}