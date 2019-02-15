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
 * Video admin controller
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Adminhtml_Nailgallery_VideoController extends Istans_Nailgallery_Controller_Adminhtml_Nailgallery
{
    /**
     * init the video
     *
     * @access protected
     * @return Istans_Nailgallery_Model_Video
     */
    protected function _initVideo()
    {
        $videoId  = (int) $this->getRequest()->getParam('id');
        $video    = Mage::getModel('istans_nailgallery/video');
        if ($videoId) {
            $video->load($videoId);
        }
        Mage::register('current_video', $video);
        return $video;
    }

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
        $this->_title(Mage::helper('istans_nailgallery')->__('Nailgallery'))
             ->_title(Mage::helper('istans_nailgallery')->__('Video'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit video - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $videoId    = $this->getRequest()->getParam('id');
        $video      = $this->_initVideo();
        if ($videoId && !$video->getId()) {
            $this->_getSession()->addError(
                Mage::helper('istans_nailgallery')->__('This video no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getVideoData(true);
        if (!empty($data)) {
            $video->setData($data);
        }
        Mage::register('video_data', $video);
        $this->loadLayout();
        $this->_title(Mage::helper('istans_nailgallery')->__('Nailgallery'))
             ->_title(Mage::helper('istans_nailgallery')->__('Video'));
        if ($video->getId()) {
            $this->_title($video->getTitle());
        } else {
            $this->_title(Mage::helper('istans_nailgallery')->__('Add video'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new video action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save video - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('video')) {
            try {
                $video = $this->_initVideo();
                $video->addData($data);
                $video->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('istans_nailgallery')->__('Video was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $video->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setVideoData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was a problem saving the video.')
                );
                Mage::getSingleton('adminhtml/session')->setVideoData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('istans_nailgallery')->__('Unable to find video to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete video - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $video = Mage::getModel('istans_nailgallery/video');
                $video->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('istans_nailgallery')->__('Video was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was an error deleting video.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('istans_nailgallery')->__('Could not find video to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete video - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $videoIds = $this->getRequest()->getParam('video');
        if (!is_array($videoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('istans_nailgallery')->__('Please select video to delete.')
            );
        } else {
            try {
                foreach ($videoIds as $videoId) {
                    $video = Mage::getModel('istans_nailgallery/video');
                    $video->setId($videoId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('istans_nailgallery')->__('Total of %d video were successfully deleted.', count($videoIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was an error deleting video.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $videoIds = $this->getRequest()->getParam('video');
        if (!is_array($videoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('istans_nailgallery')->__('Please select video.')
            );
        } else {
            try {
                foreach ($videoIds as $videoId) {
                $video = Mage::getSingleton('istans_nailgallery/video')->load($videoId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d video were successfully updated.', count($videoIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was an error updating video.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'video.csv';
        $content    = $this->getLayout()->createBlock('istans_nailgallery/adminhtml_video_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'video.xls';
        $content    = $this->getLayout()->createBlock('istans_nailgallery/adminhtml_video_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'video.xml';
        $content    = $this->getLayout()->createBlock('istans_nailgallery/adminhtml_video_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/istans_nailgallery/video');
    }
}
