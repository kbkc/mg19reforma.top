<?php

class RedInGo_Kid_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("productsGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("kid/kid")->getCollection();
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
				"index" => "kid_id",
				));

				$this->addColumn("sku", array(
				"header" => Mage::helper("kid")->__("sku"),
				"index" => "sku",
				));
				$this->addColumn("wfmag", array(
				"header" => Mage::helper("kid")->__("wfmag"),
				"index" => "wfmag",
				));
				$this->addColumn("date", array(
				"header" => Mage::helper("kid")->__("date update"),
				"index" => "date",
				));
				$this->addColumn("date_create", array(
				"header" => Mage::helper("kid")->__("date create"),
				"index" => "wfmag",
				));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}




}
