<?php 
/**
 * Istans_Nailgallery extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Istans
 * @package        Istans_Nailgallery
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Video helper
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Helper_Video extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the video list page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getVideosUrl()
    {
        if ($listKey = Mage::getStoreConfig('istans_nailgallery/video/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('istans_nailgallery/video/index');
    }

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('istans_nailgallery/video/breadcrumbs');
    }

    /**
     * check if the rss for video is enabled
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('istans_nailgallery/video/rss');
    }

    /**
     * get the link to the video rss list
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRssUrl()
    {
        return Mage::getUrl('istans_nailgallery/video/rss');
    }
}
