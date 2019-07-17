<?php
class Smartbees_Blog_Block_Hpblog extends Mage_Catalog_Block_Product_Abstract
{
    protected $_smartbeesblogdatastotemplate= null;

    public function getBlogs()
    {
        $this->_smartbeesblogdatastotemplate = Mage::getModel('smartbees_blog/observer')->adminSystemConfigChangedSectionHomepage_blog_options();
        // var_dump($this->_smartbeesblogdatastotemplate);
        for( $x=0; $x<3; $x++ ) {
            $blogTitle=Mage::getModel('blog/post')->loadByIdentifier($this->_smartbeesblogdatastotemplate[$x][0])->getTitle();
            // $blogImage=Mage::getModel('blog/post')->loadByIdentifier($this->_smartbeesblogdatastotemplate[$x][1])->getImage();
            // $blogContent=substr(Mage::getModel('blog/post')->loadByIdentifier($this->_smartbeesblogdatastotemplate[$x][1])->getPostContent(), 0, 250);
            // $blogContent = strip_tags($blogContent);
            // var_dump($blogTitle);die;
            // $this->_smartbeesblogdatastotemplate[$x][3]=$this->_smartbeesblogdatastotemplate[$x][0];
            $this->_smartbeesblogdatastotemplate[$x][2] = $blogTitle;
            // $this->_smartbeesblogdatastotemplate[$x][1] = $blogTitle;
            // if(!$blogImage) $blogImage=$this->_smartbeesblogdatastotemplate[$x][0];
            // else $this->_smartbeesblogdatastotemplate[$x][0]=$blogImage;
            // $this->_smartbeesblogdatastotemplate[$x][2]=$blogTitle;
            // $this->_smartbeesblogdatastotemplate[$x][2]=$blogTitle;
            // $this->_smartbeesblogdatastotemplate[$x][4]=$blogContent;
        }
        return $this->_smartbeesblogdatastotemplate;
    }
}