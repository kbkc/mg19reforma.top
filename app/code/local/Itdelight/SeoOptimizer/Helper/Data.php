<?php

class Itdelight_SeoOptimizer_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param $productId
     * @return string
     */
    public function getSeoProductUrl($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $path = '';
        $addStoreCode = '';
        if (Mage::getStoreConfigFlag('web/url/use_store')) {
            $storeCode = Mage::app()->getRequest()->getStoreCodeFromPath();
            if ($storeCode != Mage::app()->getDefaultStoreView()->getCode()) {
                $addStoreCode = $storeCode;
            }
        }
        foreach ($product->getCategoryIds() as $catId) {
            $pathNew = Mage::getModel('catalog/category')->load($catId)->getUrlPath();
            if (strlen($pathNew) > strlen($path)) {
                $path = $pathNew;
            }
        }
        $redirectPath = '/' . $path . $product->getUrlPath();
        if ($addStoreCode) {
            $redirectPath = '/' . $addStoreCode . $redirectPath;
        }

        return $redirectPath;
    }
}