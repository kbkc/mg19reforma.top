<?php

class Artjoker_SocialSharer_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config path to module enable
     */
    const XML_PATH_MODULE_IS_ENABLED = 'catalog/socialsharer/enabled';

    public function getSharerConfig()
    {
        $temp = Mage::app()->getConfig()->getNode('catalog/socialsharer', 'default')
            ->xpath('config');
        $temp2 = 0;
    }

    /**
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     */
    public function checkIsEnable($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_MODULE_IS_ENABLED, $store);
    }
}