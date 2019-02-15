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
 * Event front contrller
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_EventController extends Mage_Core_Controller_Front_Action
{

    /**
      * default action
      *
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('istans_nailgallery/event')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'media',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Media'),
                        'link'  => Mage::getUrl('media-en'),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'events',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Event'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('istans_nailgallery/event')->getEventsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('istans_nailgallery/event/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('istans_nailgallery/event/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('istans_nailgallery/event/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Event
     *
     * @access protected
     * @return Istans_Nailgallery_Model_Event
     * @author Ultimate Module Creator
     */
    protected function _initEvent()
    {
        $eventId   = $this->getRequest()->getParam('id', 0);
        $event     = Mage::getModel('istans_nailgallery/event')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($eventId);
        if (!$event->getId()) {
            return false;
        } elseif (!$event->getStatus()) {
            return false;
        }
        return $event;
    }

    /**
     * view event action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $event = $this->_initEvent();
        if (!$event) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_event', $event);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('nailgallery-event nailgallery-event' . $event->getId());
        }
        if (Mage::helper('istans_nailgallery/event')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('istans_nailgallery')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'events',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Event'),
                        'link'  => Mage::helper('istans_nailgallery/event')->getEventsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'event',
                    array(
                        'label' => $event->getCaption(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $event->getEventUrl());
        }
        if ($headBlock) {
            if ($event->getMetaTitle()) {
                $headBlock->setTitle($event->getMetaTitle());
            } else {
                $headBlock->setTitle($event->getCaption());
            }
            $headBlock->setKeywords($event->getMetaKeywords());
            $headBlock->setDescription($event->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * event rss list action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction()
    {
        if (Mage::helper('istans_nailgallery/event')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('nofeed', 'index', 'rss');
        }
    }
}
