<?php
class Smartbees_Promotedproducts_Block_List extends Mage_Catalog_Block_Product_Abstract
{
    protected $_itemCollection = null;
    public function getItems()
    {
        
        //var_dump($this->$_itemCollection);die;
        $catid = '252';
        if (!$catid)
            return false;
        if (is_null($this->_itemCollection)) {
            $this->_itemCollection = Mage::getModel('smartbees_promotedproducts/products')->getItemsCollection('252');
        }
        return $this->_itemCollection;
    }
}