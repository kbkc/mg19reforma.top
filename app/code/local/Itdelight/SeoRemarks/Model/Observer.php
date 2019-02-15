<?php
class Itdelight_SeoRemarks_Model_Observer extends Varien_Event_Observer
{
    public function setBlogTitle(Varien_Event_Observer $observer)
    {
        $blogTitle = Mage::helper('itdelight_seoremarks')->getPageTitle();
        Mage::app()->getLayout()->getBlock('head')->setTitle($blogTitle);
    }

    public function productVars(Varien_Event_Observer $observer)
    {
        $htmlBody = $observer->getEvent()->getControllerAction()->getResponse()->getBody();
        $productId = $observer->getEvent()->getControllerAction()->getRequest()->getParam('id');
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $search = ['{product_name}', '{product_sku}'];
        $replace = [$product->getName(), $product->getSku()];
        $htmlBody = str_replace($search, $replace, $htmlBody);
        $observer->getEvent()->getControllerAction()->getResponse()->setBody($htmlBody);
    }
}