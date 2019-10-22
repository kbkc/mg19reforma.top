<?php
class RedInGo_Kid_Block_Adminhtml_Grupa extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct()	{
        $this->_controller = "adminhtml_grupa";
        $this->_blockGroup = "kid";
        $this->_headerText = Mage::helper("kid")->__("Grupa Manager");
        $this->_addButtonLabel = Mage::helper("kid")->__("Add New Item");
        parent::__construct();
    }
}