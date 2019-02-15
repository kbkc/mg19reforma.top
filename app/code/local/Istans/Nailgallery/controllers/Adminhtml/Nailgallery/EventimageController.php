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
 * Event image admin controller
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Adminhtml_Nailgallery_EventimageController extends Istans_Nailgallery_Controller_Adminhtml_Nailgallery
{
    /**
     * init the event image
     *
     * @access protected
     * @return Istans_Nailgallery_Model_Eventimage
     */
    protected function _initEventimage()
    {
        $eventimageId  = (int) $this->getRequest()->getParam('id');
        $eventimage    = Mage::getModel('istans_nailgallery/eventimage');
        if ($eventimageId) {
            $eventimage->load($eventimageId);
        }
        Mage::register('current_eventimage', $eventimage);
        return $eventimage;
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
             ->_title(Mage::helper('istans_nailgallery')->__('Event image'));
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
     * edit event image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $eventimageId    = $this->getRequest()->getParam('id');
        $eventimage      = $this->_initEventimage();
        if ($eventimageId && !$eventimage->getId()) {
            $this->_getSession()->addError(
                Mage::helper('istans_nailgallery')->__('This event image no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getEventimageData(true);
        if (!empty($data)) {
            $eventimage->setData($data);
        }
        Mage::register('eventimage_data', $eventimage);
        $this->loadLayout();
        $this->_title(Mage::helper('istans_nailgallery')->__('Nailgallery'))
             ->_title(Mage::helper('istans_nailgallery')->__('Event image'));
        if ($eventimage->getId()) {
            $this->_title($eventimage->getTitle());
        } else {
            $this->_title(Mage::helper('istans_nailgallery')->__('Add event image'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new event image action
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
     * save event image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('eventimage')) {
            try {
                $eventimage = $this->_initEventimage();
                $eventimage->addData($data);
                $imageName = $this->_uploadAndGetName(
                    'file',
                    Mage::helper('istans_nailgallery/eventimage_image')->getImageBaseDir(),
                    $data
                );
                $eventimage->setData('file', $imageName);
                $eventimage->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('istans_nailgallery')->__('Event image was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $eventimage->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                if (isset($data['file']['value'])) {
                    $data['file'] = $data['file']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setEventimageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                if (isset($data['file']['value'])) {
                    $data['file'] = $data['file']['value'];
                }
                
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was a problem saving the event image.')
                );
                Mage::getSingleton('adminhtml/session')->setEventimageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('istans_nailgallery')->__('Unable to find event image to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete event image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $eventimage = Mage::getModel('istans_nailgallery/eventimage');
                $eventimage->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('istans_nailgallery')->__('Event image was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was an error deleting event image.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('istans_nailgallery')->__('Could not find event image to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete event image - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $eventimageIds = $this->getRequest()->getParam('eventimage');
        if (!is_array($eventimageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('istans_nailgallery')->__('Please select event image to delete.')
            );
        } else {
            try {
                foreach ($eventimageIds as $eventimageId) {
                    $eventimage = Mage::getModel('istans_nailgallery/eventimage');
                    $eventimage->setId($eventimageId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('istans_nailgallery')->__('Total of %d event image were successfully deleted.', count($eventimageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was an error deleting event image.')
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
        $eventimageIds = $this->getRequest()->getParam('eventimage');
        if (!is_array($eventimageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('istans_nailgallery')->__('Please select event image.')
            );
        } else {
            try {
                foreach ($eventimageIds as $eventimageId) {
                $eventimage = Mage::getSingleton('istans_nailgallery/eventimage')->load($eventimageId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d event image were successfully updated.', count($eventimageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was an error updating event image.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass event change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massEventIdAction()
    {
        $eventimageIds = $this->getRequest()->getParam('eventimage');
        if (!is_array($eventimageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('istans_nailgallery')->__('Please select event image.')
            );
        } else {
            try {
                foreach ($eventimageIds as $eventimageId) {
                $eventimage = Mage::getSingleton('istans_nailgallery/eventimage')->load($eventimageId)
                    ->setEventId($this->getRequest()->getParam('flag_event_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d event image were successfully updated.', count($eventimageIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('istans_nailgallery')->__('There was an error updating event image.')
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
        $fileName   = 'eventimage.csv';
        $content    = $this->getLayout()->createBlock('istans_nailgallery/adminhtml_eventimage_grid')
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
        $fileName   = 'eventimage.xls';
        $content    = $this->getLayout()->createBlock('istans_nailgallery/adminhtml_eventimage_grid')
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
        $fileName   = 'eventimage.xml';
        $content    = $this->getLayout()->createBlock('istans_nailgallery/adminhtml_eventimage_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('cms/istans_nailgallery/eventimage');
    }
}
