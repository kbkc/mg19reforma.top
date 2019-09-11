<?php
class RedInGo_Kid_Block_Adminhtml_Status extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct()    {
        $this->_controller = "adminhtml_status";
        $this->_blockGroup = "kid";
        $this->_headerText = Mage::helper("kid")->__("Status Manager");
        $this->_addButtonLabel = Mage::helper("kid")->__("Dodaj grupe");
        parent::__construct();
    }
}