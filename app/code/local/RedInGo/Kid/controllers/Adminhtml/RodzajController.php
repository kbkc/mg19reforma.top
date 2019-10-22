<?php

class RedInGo_Kid_Adminhtml_RodzajController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()    {
        $this->loadLayout()->_setActiveMenu("kid/rodzaj")->_addBreadcrumb(Mage::helper("adminhtml")->__("Rodzaj  Manager"),Mage::helper("adminhtml")->__("Rodzaj Manager"));
        return $this;
    }
    public function indexAction()     {
        $this->_title($this->__("Kid"));
        $this->_title($this->__("Manager Rodzaj"));

        $this->_initAction();
        $this->renderLayout();
    }
    public function editAction()    {			    
        $this->_title($this->__("Kid"));
        $this->_title($this->__("Rodzaj"));
        $this->_title($this->__("Edit Item"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("kid/rodzaj")->load($id);
        if ($model->getId()) {
            Mage::register("rodzaj_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("kid/rodzaj");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rodzaj Manager"), Mage::helper("adminhtml")->__("Rodzaj Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rodzaj Description"), Mage::helper("adminhtml")->__("Rodzaj Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("kid/adminhtml_rodzaj_edit"))->_addLeft($this->getLayout()->createBlock("kid/adminhtml_rodzaj_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("kid")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()    {

        $this->_title($this->__("Kid"));
        $this->_title($this->__("Rodzaj"));
        $this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
        $model  = Mage::getModel("kid/rodzaj")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
                $model->setData($data);
        }

        Mage::register("rodzaj_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("kid/rodzaj");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rodzaj Manager"), Mage::helper("adminhtml")->__("Rodzaj Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rodzaj Description"), Mage::helper("adminhtml")->__("Rodzaj Description"));

        $this->_addContent($this->getLayout()->createBlock("kid/adminhtml_rodzaj_edit"))->_addLeft($this->getLayout()->createBlock("kid/adminhtml_rodzaj_edit_tabs"));

        $this->renderLayout();
    }
    public function saveAction()    {
            $post_data=$this->getRequest()->getPost();
            if ($post_data) {
                try {
                    $model = Mage::getModel("kid/rodzaj")
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();
                    Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Rodzaj was successfully saved"));
                    Mage::getSingleton("adminhtml/session")->setRodzajData(false);

                    if ($this->getRequest()->getParam("back")) {
                            $this->_redirect("*/*/edit", array("id" => $model->getId()));
                            return;
                    }
                    $this->_redirect("*/*/");
                    return;
                } 
                catch (Exception $e) {
                    Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                    Mage::getSingleton("adminhtml/session")->setRodzajData($this->getRequest()->getPost());
                    $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                    return;
                }
            }
            $this->_redirect("*/*/");
    }

    public function deleteAction()    {
        if( $this->getRequest()->getParam("id") > 0 ) {
            try {
                $model = Mage::getModel("kid/rodzaj");
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


    public function massRodzajAction()    {
        echo "<pre>";
        try {
            $ids = $this->getRequest()->getPost('ids', array());
            print_r($ids);
            foreach ($ids as $id) {
                $model = Mage::getModel("kid/rodzaj")->load( $id );
                
                $products = Mage::getResourceModel('catalog/product_collection');
                $products->addAttributeToSelect('*');

                $kategoriaId = Mage::getResourceModel('catalog/product')
                                ->getAttribute('kategoria')
                                ->getSource()
                                ->getOptionId('Papilotki');

                $products->addAttributeToFilter('kategoria', $kategoriaId);
	
                foreach ($products as $value) {
                    $p = array(
                        'sku' => $value->getSku(),
                    );
                }

                
                exit();
                $model->setId($id)->delete();
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
        $fileName   = 'rodzaj.csv';
        $grid       = $this->getLayout()->createBlock('kid/adminhtml_rodzaj_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    } 
    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'rodzaj.xml';
        $grid       = $this->getLayout()->createBlock('kid/adminhtml_rodzaj_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
