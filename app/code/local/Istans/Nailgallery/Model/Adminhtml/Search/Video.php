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
 * Admin search model
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Model_Adminhtml_Search_Video extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Istans_Nailgallery_Model_Adminhtml_Search_Video
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('istans_nailgallery/video_collection')
            ->addFieldToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $video) {
            $arr[] = array(
                'id'          => 'video/1/'.$video->getId(),
                'type'        => Mage::helper('istans_nailgallery')->__('Video'),
                'name'        => $video->getTitle(),
                'description' => $video->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/nailgallery_video/edit',
                    array('id'=>$video->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
