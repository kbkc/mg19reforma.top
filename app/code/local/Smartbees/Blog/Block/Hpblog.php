<?php
class Smartbees_Blog_Block_Hpblog extends Mage_Catalog_Block_Product_Abstract
{
    protected $_smartbeesblogdatastotemplate= null;

    public function getBlogs()
    {
        $this->_smartbeesblogdatastotemplate = Mage::getModel('smartbees_blog/observer')->adminSystemConfigChangedSectionHomepage_blog_options();

        for( $x=0; $x<3; $x++ ) {
            $blogTitle=Mage::getModel('blog/post')->loadByIdentifier($this->_smartbeesblogdatastotemplate[$x][0])->getTitle();
            $this->_smartbeesblogdatastotemplate[$x][2] = $blogTitle;
        }
        return $this->_smartbeesblogdatastotemplate;
    }
}