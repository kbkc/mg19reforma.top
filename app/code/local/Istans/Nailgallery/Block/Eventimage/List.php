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
 * Event image list block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Eventimage_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $eventimages = Mage::getResourceModel('istans_nailgallery/eventimage_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        //$eventimages->setOrder('order');
        $eventimages->setOrder('title', 'asc');
        $this->setEventimages($eventimages);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Eventimage_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'istans_nailgallery.eventimage.html.pager'
        )
        ->setCollection($this->getEventimages());
        $this->setChild('pager', $pager);
        $this->getEventimages()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
