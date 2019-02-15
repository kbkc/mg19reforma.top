<?php
class Itdelight_SeoRemarks_Block_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    public function getProductUrl()
    {
        $url = Mage::helper('seooptimizer')->getSeoProductUrl($this->getProduct()->getId());
        return $url;
    }
}