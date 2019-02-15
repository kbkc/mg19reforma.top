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
 * Event list block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Event_List extends Mage_Core_Block_Template
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
        $events = Mage::getResourceModel('istans_nailgallery/event_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        $events->setOrder('caption', 'asc');
        $this->setEvents($events);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Event_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'istans_nailgallery.event.html.pager'
        )
        ->setCollection($this->getEvents());
        $this->setChild('pager', $pager);
        $this->getEvents()->load();
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
