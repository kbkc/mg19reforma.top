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
 * Event Event image list block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Event_Eventimage_List extends Istans_Nailgallery_Block_Eventimage_List
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $event = $this->getEvent();
        if ($event) {
            $this->getEventimages()->addFieldToFilter('event_id', $event->getId());
        }
    }

    /**
     * prepare the layout - actually do nothing
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Event_Eventimage_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * get the current event
     *
     * @access public
     * @return Istans_Nailgallery_Model_Event
     * @author Ultimate Module Creator
     */
    public function getEvent()
    {
        return Mage::registry('current_event');
    }
}
