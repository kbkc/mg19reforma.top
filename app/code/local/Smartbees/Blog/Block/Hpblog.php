<?php
class Smartbees_Blog_Block_Hpblog extends Mage_Catalog_Block_Product_Abstract
{
    protected $_smartbeesblogdatastotemplate= null;
    public function getBlogs()
    {
        $this->_smartbeesblogdatastotemplate = Mage::getModel('smartbees_blog/observer')->adminSystemConfigChangedSectionHomepage_blog_options();

        for( $x=0; $x<3; $x++ ) {
            $blogTitle=Mage::getModel('blog/post')->loadByIdentifier($this->_smartbeesblogdatastotemplate[$x][1])->getTitle();
            $blogImage=Mage::getModel('blog/post')->loadByIdentifier($this->_smartbeesblogdatastotemplate[$x][1])->getImage();
            
            $this->_smartbeesblogdatastotemplate[$x][3]=$this->_smartbeesblogdatastotemplate[$x][0];

            if(!$blogImage) $blogImage=$this->_smartbeesblogdatastotemplate[$x][0];
            else $this->_smartbeesblogdatastotemplate[$x][0]=$blogImage;
            $this->_smartbeesblogdatastotemplate[$x][2]=$blogTitle;
            $this->_smartbeesblogdatastotemplate[$x][2]=$blogTitle;
            // var_dump($blogImage);
        }
        return $this->_smartbeesblogdatastotemplate;
    }
}