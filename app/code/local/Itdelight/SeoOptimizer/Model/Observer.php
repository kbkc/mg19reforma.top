<?php

class Itdelight_SeoOptimizer_Model_Observer extends Varien_Event_Observer
{
    public function productUrlRedirect($observer)
    {
        $storeCode = Mage::app()->getRequest()->getStoreCodeFromPath();
        $currUrl = Mage::app()->getRequest()->getOriginalPathInfo();
        if ($storeCode != Mage::app()->getDefaultStoreView()->getCode()) {
            $currUrl = '/' . $storeCode . Mage::app()->getRequest()->getOriginalPathInfo();
        }

        $redirectPath = Mage::helper('seooptimizer')->getSeoProductUrl(Mage::app()->getRequest()->getParam('id'));
        if ($redirectPath == $currUrl) {
            return;
        } else {
            Mage::app()->getResponse()->setRedirect($redirectPath);
        }
    }
}