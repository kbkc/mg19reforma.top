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
 * Event RSS block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Event_Rss extends Mage_Rss_Block_Abstract
{
    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_nailgallery_event_rss';

    /**
     * constructor
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->setCacheTags(array(self::CACHE_TAG));
        /*
         * setting cache to save the rss for 10 minutes
         */
        $this->setCacheKey('istans_nailgallery_event_rss');
        $this->setCacheLifetime(600);
    }

    /**
     * toHtml method
     *
     * @access protected
     * @return string
     * @author Ultimate Module Creator
     */
    protected function _toHtml()
    {
        $url    = Mage::helper('istans_nailgallery/event')->getEventsUrl();
        $title  = Mage::helper('istans_nailgallery')->__('Event');
        $rssObj = Mage::getModel('rss/rss');
        $data  = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $url,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);
        $collection = Mage::getModel('istans_nailgallery/event')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('in_rss', 1)
            ->setOrder('created_at');
        $collection->load();
        foreach ($collection as $item) {
            $description = '<p>';
            if ($item->getImage()) {
                $description .= '<div>';
                $description .= Mage::helper('istans_nailgallery')->__('Image');
                $description .= '    <a href="'.Mage::helper('istans_nailgallery/event')->getFileBaseUrl().$item->getImage().'">';
                $description .= '        <span>'. basename($item->getImage()).'</span>';
                $description .= '    </a>';
                $description .= '</div>';
            }
            $description .= '<div>'.
                Mage::helper('istans_nailgallery')->__('Caption').': 
                '.$item->getCaption().
                '</div>';
            $description .= '</p>';
            $data = array(
                'title'       => $item->getCaption(),
                'link'        => $item->getEventUrl(),
                'description' => $description
            );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}
