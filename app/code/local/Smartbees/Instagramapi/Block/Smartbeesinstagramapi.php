<?php
class Smartbees_Instagramapi_Block_Smartbeesinstagramapi extends Mage_Catalog_Block_Product_Abstract
{
    protected $urlIMG = '';
    public function getInstagramPictures()
    {
        
        $this->urlIMG = Mage::getModel('smartbees_instagramapi/instagramapi')->getImagesUrl();
        return $this->urlIMG;
    }
}