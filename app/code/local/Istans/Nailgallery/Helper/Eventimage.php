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
 * Event image helper
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Helper_Eventimage extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the event image list page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getEventimagesUrl()
    {
        if ($listKey = Mage::getStoreConfig('istans_nailgallery/eventimage/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('istans_nailgallery/eventimage/index');
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
        return Mage::getStoreConfigFlag('istans_nailgallery/eventimage/breadcrumbs');
    }

    /**
     * check if the rss for event image is enabled
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('istans_nailgallery/eventimage/rss');
    }

    /**
     * get the link to the event image rss list
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRssUrl()
    {
        return Mage::getUrl('istans_nailgallery/eventimage/rss');
    }
}
