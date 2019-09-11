<?php

class RedInGo_Kid_Adminhtml_GrupaController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()    {
        $this->loadLayout()->_setActiveMenu("kid/grupa")->_addBreadcrumb(Mage::helper("adminhtml")->__("Grupa  Manager"),Mage::helper("adminhtml")->__("Grupa Manager"));
        return $this;
    }
    public function indexAction()     {
        $this->_title($this->__("Kid"));
        $this->_title($this->__("Manager Grupa"));

        $this->_initAction();
        $this->renderLayout();
    }
    public function editAction()		{			    
        $this->_title($this->__("Kid"));
        $this->_title($this->__("Grupa"));
        $this->_title($this->__("Edit Item"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("kid/grupa")->load($id);
        if ($model->getId()) {
            Mage::register("grupa_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("kid/grupa");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Grupa Manager"), Mage::helper("adminhtml")->__("Grupa Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Grupa Description"), Mage::helper("adminhtml")->__("Grupa Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("kid/adminhtml_grupa_edit"))->_addLeft($this->getLayout()->createBlock("kid/adminhtml_grupa_edit_tabs"));
            $this->renderLayout();
        } 
        else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("kid")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()		{

        $this->_title($this->__("Kid"));
        $this->_title($this->__("Grupa"));
        $this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
        $model  = Mage::getModel("kid/grupa")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
                $model->setData($data);
        }

        Mage::register("grupa_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("kid/grupa");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Grupa Manager"), Mage::helper("adminhtml")->__("Grupa Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Grupa Description"), Mage::helper("adminhtml")->__("Grupa Description"));


        $this->_addContent($this->getLayout()->createBlock("kid/adminhtml_grupa_edit"))->_addLeft($this->getLayout()->createBlock("kid/adminhtml_grupa_edit_tabs"));

        $this->renderLayout();

    }
    
    public function saveAction()		{

        $post_data=$this->getRequest()->getPost();

        if ($post_data) {
            try {
                $model = Mage::getModel("kid/grupa")
                ->addData($post_data)
                ->setId($this->getRequest()->getParam("id"))
                ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Grupa was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setGrupaData(false);

                if ($this->getRequest()->getParam("back")) {
                        $this->_redirect("*/*/edit", array("id" => $model->getId()));
                        return;
                }
                $this->_redirect("*/*/");
                return;
            } 
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setGrupaData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
        }
        $this->_redirect("*/*/");
    }
    
    public function deleteAction()
    {
        if( $this->getRequest()->getParam("id") > 0 ) {
                try {
                        $model = Mage::getModel("kid/grupa");
                        $model->setId($this->getRequest()->getParam("id"))->delete();
                        Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
                        $this->_redirect("*/*/");
                } 
                catch (Exception $e) {
                        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                        $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                }
        }
        $this->_redirect("*/*/");
    }

		
    public function massProductAction()
    {
       // $this->saveAction();
        try {
                $ids = array();
                $ids = $this->getRequest()->getPost('ids', array());
                if( count($ids) !=1 ){
                    $ids[] = explode(',',$this->getRequest()->getParam('ids'));
                }
                
                
                foreach ($ids as $id) {
                    $model = Mage::getModel("kid/grupa")->load($id);

        
                    switch ( $model->getType() ) {
                        case 'configurable':
                            $model->setId($id)->conf();
                            exit();
                            break;
                        case 'grouped':
                            $model->generoj();
                            break;
                        default:
                            break;
                    }
                    unset( $model );
                    //$model->setId($id)->conf();
                }

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__( count($ids) ));
        }
        catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    public function massRemoveAction()
    {
        try {
                $ids = $this->getRequest()->getPost('ids', array());
                foreach ($ids as $id) {
                    $model = Mage::getModel("kid/grupa")->load($id);
                    $model->delete();
                }
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
        }
        catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'grupa.csv';
			$grid       = $this->getLayout()->createBlock('kid/adminhtml_grupa_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'grupa.xml';
			$grid       = $this->getLayout()->createBlock('kid/adminhtml_grupa_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
