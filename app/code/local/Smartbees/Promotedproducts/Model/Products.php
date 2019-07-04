<?php
class Smartbees_Promotedproducts_Model_Products extends Mage_Catalog_Model_Product
{
    public function getItemsCollection($valueId)
    {
        $category = Mage::getModel('catalog/category')->load($valueId);
        $productCollection = $category->getProductCollection();
        $productCollection
                ->addStoreFilter()
                ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addUrlRewrite();
        return $productCollection;
    }
}