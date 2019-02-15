<?php
class Istans_Quickorder_Block_Index extends Mage_Catalog_Block_Product_Abstract
{
    public function _getProducts($catId = null)
    {
        $sortName = Mage::app()->getRequest()->getParam('sort');
        $sortDir = Mage::app()->getRequest()->getParam('dir');
        if(!isset($sortDir))$sortDir='asc';

        /**
         * @var $collection Mage_Catalog_Model_Resource_Product_Collection
         */
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('in_stock')
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
//        if ($catId) {
//            $collection->addCategoryFilter($catId);
//        }

//        if($sortName) {
//            $collection->addAttributeToSort($sortName, $sortDir);
//        }

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }

        return $collection->getItems();
    }

    public function _getCategories()
    {
      //  $categories = Mage::getModel('catalog/category')->getCollection();
        $collection = Mage::getModel('catalog/category')
            ->load(102)->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('catalogue_display', 1)
            ->addAttributeToSort('position');


        return $collection;
    }


}