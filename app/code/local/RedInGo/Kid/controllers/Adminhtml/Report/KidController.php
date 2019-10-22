<?php
class RedInGo_Kid_Adminhtml_Report_KidController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Kid Report'));

        $this->loadLayout()
            ->_setActiveMenu('kid/kid')
            ->_addBreadcrumb(Mage::helper('reports')->__('Kid Report'),
                Mage::helper('reports')->__('Kid Report'))
            ->_addContent($this->getLayout()->createBlock('kid/adminhtml_report_kid'))
            ->renderLayout();
    }
}