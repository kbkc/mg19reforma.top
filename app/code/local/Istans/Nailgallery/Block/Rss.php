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
 * Nailgallery RSS block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Rss extends Mage_Core_Block_Template
{
    /**
     * RSS feeds for this block
     */
    protected $_feeds = array();

    /**
     * add a new feed
     *
     * @access public
     * @param string $label
     * @param string $url
     * @param bool $prepare
     * @return Istans_Nailgallery_Block_Rss
     * @author Ultimate Module Creator
     */
    public function addFeed($label, $url, $prepare = false)
    {
        $link = ($prepare ? $this->getUrl($url) : $url);
        $feed = new Varien_Object();
        $feed->setLabel($label);
        $feed->setUrl($link);
        $this->_feeds[$link] = $feed;
        return $this;
    }

    /**
     * get the current feeds
     *
     * @access public
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getFeeds()
    {
        return $this->_feeds;
    }
}
