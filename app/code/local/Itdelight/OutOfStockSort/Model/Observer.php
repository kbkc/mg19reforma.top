<?php

/**
 * Class Itdelight_OutOfStockSort_Model_Observer
 */
class Itdelight_OutOfStockSort_Model_Observer extends Varien_Event_Observer
{
    /**
     * Moves out of stock products to the end of category
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function outOfStockSort(Varien_Event_Observer $observer)
    {
        if (Mage::registry('dontUseItDelightObserver')) {
            return;
        }
        if (!Mage::getStoreConfigFlag('cataloginventory/options/outofstock_to_end')) {
            return;
        }
        $toolbar = Mage::getBlockSingleton('catalog/product_list_toolbar');
        if ($toolbar) {
            /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
            $products = $observer->getEvent()->getCollection();

            $stockId = Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
            $websiteId = Mage::app()->getStore($products->getStoreId())->getWebsiteId();

            $products->getSelect()->joinLeft(
                array('_inv' => $products->getResource()->getTable('cataloginventory/stock_status')),
                "_inv.product_id = e.entity_id and _inv.website_id=$websiteId and _inv.stock_id=$stockId",
                array('stock_status')
            );
            $products->addExpressionAttributeToSelect('in_stock', 'IFNULL(_inv.stock_status,0)', array());

            $products->getSelect()->reset('order');
            $products->getSelect()->order('in_stock DESC');

            if ($toolbar->getCurrentOrder()) {
                $products->addAttributeToSort($toolbar->getCurrentOrder(), $toolbar->getCurrentDirection());
            }
        }

        return $this;
    }
}