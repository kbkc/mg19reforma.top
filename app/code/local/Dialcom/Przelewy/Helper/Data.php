<?php

/**
 * Class Dialcom_Przelewy_Helper_Data
 */
class Dialcom_Przelewy_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStoreID()
    {
        if($this->isAdmin()) {
            $storeID = $this->getAdminStoreID();
        } else {
            $storeID = $this->getFrontStoreID();
        }

        return $storeID;
    }

    /**
     * @return int
     */
    public function getAdminStoreID()
    {
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) {
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) {
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } else {
            $store_id = 0;
        }

        return $store_id;
    }

    public function getFrontStoreID()
    {
        $store_id = Mage::app()->getStore()->getStoreId();

        return $store_id;
    }

    public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin())
        {
            return true;
        }

        if(Mage::getDesign()->getArea() == 'adminhtml')
        {
            return true;
        }

        return false;
    }
}