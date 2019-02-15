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
 * Event image front contrller
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_EventimageController extends Mage_Core_Controller_Front_Action
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
        if (Mage::helper('istans_nailgallery/eventimage')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'eventimages',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Event image'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('istans_nailgallery/eventimage')->getEventimagesUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('istans_nailgallery/eventimage/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('istans_nailgallery/eventimage/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('istans_nailgallery/eventimage/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Event image
     *
     * @access protected
     * @return Istans_Nailgallery_Model_Eventimage
     * @author Ultimate Module Creator
     */
    protected function _initEventimage()
    {
        $eventimageId   = $this->getRequest()->getParam('id', 0);
        $eventimage     = Mage::getModel('istans_nailgallery/eventimage')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($eventimageId);
        if (!$eventimage->getId()) {
            return false;
        } elseif (!$eventimage->getStatus()) {
            return false;
        }
        return $eventimage;
    }

    /**
     * view event image action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $eventimage = $this->_initEventimage();
        if (!$eventimage) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_eventimage', $eventimage);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('nailgallery-eventimage nailgallery-eventimage' . $eventimage->getId());
        }
        if (Mage::helper('istans_nailgallery/eventimage')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('istans_nailgallery')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'eventimages',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Event image'),
                        'link'  => Mage::helper('istans_nailgallery/eventimage')->getEventimagesUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'eventimage',
                    array(
                        'label' => $eventimage->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $eventimage->getEventimageUrl());
        }
        if ($headBlock) {
            if ($eventimage->getMetaTitle()) {
                $headBlock->setTitle($eventimage->getMetaTitle());
            } else {
                $headBlock->setTitle($eventimage->getTitle());
            }
            $headBlock->setKeywords($eventimage->getMetaKeywords());
            $headBlock->setDescription($eventimage->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * event image rss list action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction()
    {
        if (Mage::helper('istans_nailgallery/eventimage')->isRssEnabled()) {
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
