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
 * Video front contrller
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_VideoController extends Mage_Core_Controller_Front_Action
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
        if (Mage::helper('istans_nailgallery/video')->getUseBreadcrumbs()) {
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
                    'videos',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Video'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('istans_nailgallery/video')->getVideosUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('istans_nailgallery/video/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('istans_nailgallery/video/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('istans_nailgallery/video/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Video
     *
     * @access protected
     * @return Istans_Nailgallery_Model_Video
     * @author Ultimate Module Creator
     */
    protected function _initVideo()
    {
        $videoId   = $this->getRequest()->getParam('id', 0);
        $video     = Mage::getModel('istans_nailgallery/video')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($videoId);
        if (!$video->getId()) {
            return false;
        } elseif (!$video->getStatus()) {
            return false;
        }
        return $video;
    }

    /**
     * view video action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $video = $this->_initVideo();
        if (!$video) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_video', $video);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('nailgallery-video nailgallery-video' . $video->getId());
        }
        if (Mage::helper('istans_nailgallery/video')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('istans_nailgallery')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'videos',
                    array(
                        'label' => Mage::helper('istans_nailgallery')->__('Video'),
                        'link'  => Mage::helper('istans_nailgallery/video')->getVideosUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'video',
                    array(
                        'label' => $video->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $video->getVideoUrl());
        }
        if ($headBlock) {
            if ($video->getMetaTitle()) {
                $headBlock->setTitle($video->getMetaTitle());
            } else {
                $headBlock->setTitle($video->getTitle());
            }
            $headBlock->setKeywords($video->getMetaKeywords());
            $headBlock->setDescription($video->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * video rss list action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction()
    {
        if (Mage::helper('istans_nailgallery/video')->isRssEnabled()) {
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
