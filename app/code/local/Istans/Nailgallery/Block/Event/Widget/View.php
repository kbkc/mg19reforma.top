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
 * Event widget block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Event_Widget_View extends Mage_Core_Block_Template implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'istans_nailgallery/event/widget/view.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Event_Widget_View
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $eventId = $this->getData('event_id');
        if ($eventId) {
            $event = Mage::getModel('istans_nailgallery/event')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($eventId);
            if ($event->getStatus()) {
                $this->setCurrentEvent($event);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}
