<?php
class Itdelight_SeoRemarks_Model_Product extends Mage_Catalog_Model_Product
{
    /**
     * Retrieve Product URL
     *
     * @param  bool $useSid
     * @return string
     */
    public function getProductUrl($useSid = null)
    {
        $redirectPath = Mage::helper('seooptimizer')->getSeoProductUrl($this->getId());
        return $redirectPath;
    }
}