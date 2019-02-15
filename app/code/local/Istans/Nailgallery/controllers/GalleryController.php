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
 * Gallery front contrller
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_GalleryController extends Mage_Core_Controller_Front_Action
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
        if (Mage::helper('istans_nailgallery/gallery')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
      /*
            $breadcrumbBlock->addCrumb(
                    'media',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Media'),
                        'link'  => Mage::getUrl('media-en'),
                    )
                );
      */
                $breadcrumbBlock->addCrumb(
                    'gallerys',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Gallery'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('istans_nailgallery/gallery')->getGallerysUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('istans_nailgallery/gallery/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('istans_nailgallery/gallery/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('istans_nailgallery/gallery/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Gallery
     *
     * @access protected
     * @return Istans_Nailgallery_Model_Gallery
     * @author Ultimate Module Creator
     */
    protected function _initGallery()
    {
        $galleryId   = $this->getRequest()->getParam('id', 0);
        $gallery     = Mage::getModel('istans_nailgallery/gallery')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($galleryId);
        if (!$gallery->getId()) {
            return false;
        } elseif (!$gallery->getStatus()) {
            return false;
        }
        return $gallery;
    }

    /**
     * view gallery action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $gallery = $this->_initGallery();
        if (!$gallery) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_gallery', $gallery);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('nailgallery-gallery nailgallery-gallery' . $gallery->getId());
        }
        if (Mage::helper('istans_nailgallery/gallery')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('istans_nailgallery')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'gallerys',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Gallery'),
                        'link'  => Mage::helper('istans_nailgallery/gallery')->getGallerysUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'gallery',
                    array(
                        'label' => $gallery->getCaption(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $gallery->getGalleryUrl());
        }
        if ($headBlock) {
            if ($gallery->getMetaTitle()) {
                $headBlock->setTitle($gallery->getMetaTitle());
            } else {
                $headBlock->setTitle($gallery->getCaption());
            }
            $headBlock->setKeywords($gallery->getMetaKeywords());
            $headBlock->setDescription($gallery->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * gallery rss list action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction()
    {
        if (Mage::helper('istans_nailgallery/gallery')->isRssEnabled()) {
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
