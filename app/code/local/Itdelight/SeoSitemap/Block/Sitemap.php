<?php

class Itdelight_SeoSitemap_Block_Sitemap extends Mage_Core_Block_Template
{

    public function generateSitemap()
    {
        $items = array();
        $StoreId = Mage::app()->getStore()->getStoreId();
        /**
         * Generate categories sitemap
         */
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('is_active', array('eq' => '1'))
            ->setStoreId($StoreId)
            ->getItems();
        foreach ($collection as $item) {
            $items[] = $item->getUrl();
        }
        unset($collection);

        /**
         * Generate products sitemap
         */
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('status', array('eq' => 1))
            ->setStoreId($StoreId)
            ->getItems();
        foreach ($collection as $item) {

            $categoryUrlKey = '';
            foreach (Mage::getModel('catalog/product')->load($item->getId())->getCategoryIds() as $categoryId) {
                $categoryUrlKey .= Mage::getModel('catalog/category')->load($categoryId)->getUrlKey() . '/';
            }

            $items[] = Mage::getBaseUrl() . $categoryUrlKey . $item->getRequestPath();
        }
        unset($collection);

        /**
         * Generate cms pages sitemap
         */
        $collection = Mage::getResourceModel('cms/page_collection')
            ->addStoreFilter($StoreId, $withAdmin = true)
            ->addFieldToFilter('is_active', 1)
            ->getItems();
        foreach ($collection as $item) {
            if ($link = Mage::helper('cms/page')->getPageUrl($item->getId())) {
                $items[] = $link;
            }
        }
        unset($collection);

        $sitemap = array_chunk($items, 100);
        $countPages = count($sitemap);

        if ($page = Mage::app()->getRequest()->getParam('p')) {
            $sitemap = $sitemap[$page - 1];
        } else {
            $sitemap = $sitemap[0];
        }

        if ($sitemap == null) {
            Mage::app()->getResponse()->setRedirect(404)->sendResponse();
        }

        return array(
            'data' => $sitemap,
            'pages' => $countPages
        );
    }
}