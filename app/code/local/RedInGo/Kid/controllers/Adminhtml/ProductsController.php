<?php

class RedInGo_Kid_Adminhtml_ProductsController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('kid/products');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("kid/products")->_addBreadcrumb(Mage::helper("adminhtml")->__("Products  Manager"),Mage::helper("adminhtml")->__("Products Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Kid"));
			    $this->_title($this->__("Manager Products"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Kid"));
				$this->_title($this->__("Products"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("kid/products")->load($id);
				if ($model->getId()) {
					Mage::register("products_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("kid/products");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Products Manager"), Mage::helper("adminhtml")->__("Products Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Products Description"), Mage::helper("adminhtml")->__("Products Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("kid/adminhtml_products_edit"))->_addLeft($this->getLayout()->createBlock("kid/adminhtml_products_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("kid")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Kid"));
		$this->_title($this->__("Products"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("kid/products")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("products_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("kid/products");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Products Manager"), Mage::helper("adminhtml")->__("Products Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Products Description"), Mage::helper("adminhtml")->__("Products Description"));


		$this->_addContent($this->getLayout()->createBlock("kid/adminhtml_products_edit"))->_addLeft($this->getLayout()->createBlock("kid/adminhtml_products_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("kid/products")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Products was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setProductsData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setProductsData($this->getRequest()->getPost());
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
						$model = Mage::getModel("kid/products");
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

		
}
