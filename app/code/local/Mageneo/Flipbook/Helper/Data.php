<?php

class Mageneo_Flipbook_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_MODULE_IS_ENABLED = 'mageneo/flipbook/enabled';

    /**
     * @param mixed $store
     *
     * @return boolean
     */
    public function moduleIsEnabled($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_MODULE_IS_ENABLED, $store) ? true : false;
    }
}