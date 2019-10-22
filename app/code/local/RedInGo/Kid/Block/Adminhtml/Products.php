<?php


class RedInGo_Kid_Block_Adminhtml_Products extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_products";
	$this->_blockGroup = "kid";
	$this->_headerText = Mage::helper("kid")->__("Products Manager");
	$this->_addButtonLabel = Mage::helper("kid")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}