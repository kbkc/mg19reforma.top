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
class Istans_Nailgallery_Model_Adminhtml_Search_Eventimage extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Istans_Nailgallery_Model_Adminhtml_Search_Eventimage
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('istans_nailgallery/eventimage_collection')
            ->addFieldToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $eventimage) {
            $arr[] = array(
                'id'          => 'eventimage/1/'.$eventimage->getId(),
                'type'        => Mage::helper('istans_nailgallery')->__('Event image'),
                'name'        => $eventimage->getTitle(),
                'description' => $eventimage->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/nailgallery_eventimage/edit',
                    array('id'=>$eventimage->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
