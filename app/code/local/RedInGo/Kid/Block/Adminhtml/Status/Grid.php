<?php

class RedInGo_Kid_Block_Adminhtml_Status_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("statusGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}
		protected function _prepareCollection()
		{
				$collection = Mage::getModel("kid/status")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("kid")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("kid_status", array(
				"header" => Mage::helper("kid")->__("kid_status"),
				"index" => "kid_status",
				));
				$this->addColumn("status", array(
				"header" => Mage::helper("kid")->__("status"),
				"index" => "status",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}
		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}
		static public function getValueArray1()
		{
			$options = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
			$source =array();
			foreach ($options as $item) {
				$source[ $item["status"] ] = $item["label"] ;
			}
			return $source;
		}
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_status', array(
					 'label'=> Mage::helper('kid')->__('Remove Status'),
					 'url'  => $this->getUrl('*/adminhtml_status/massRemove'),
					 'confirm' => Mage::helper('kid')->__('Are you sure?')
				));
			return $this;
		}
			

}