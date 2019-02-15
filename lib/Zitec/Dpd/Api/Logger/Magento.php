<?php

class Zitec_Dpd_Api_Logger_Magento extends Zitec_Dpd_Api_Logger_Abstract
{
    public function log($_message)
    {
        Mage::log($_message, null, $this->file, true);
    }
}