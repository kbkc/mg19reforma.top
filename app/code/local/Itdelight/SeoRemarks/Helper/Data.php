<?php
class Itdelight_SeoRemarks_Helper_Data extends Magiccart_Blog_Helper_Data
{
    const XML_PATH_PAGE_TITLE = 'blog/blog/page_title';

    public function getPageTitle($store = null)
    {
        return $this->conf(self::XML_PATH_PAGE_TITLE, $store);
    }
}