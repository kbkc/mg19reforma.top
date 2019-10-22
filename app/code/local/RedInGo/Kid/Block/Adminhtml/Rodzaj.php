<?php
class RedInGo_Kid_Block_Adminhtml_Rodzaj extends Mage_Adminhtml_Block_Widget_Grid_Container{

    public function __construct()    {
        $this->_controller = "adminhtml_rodzaj";
        $this->_blockGroup = "kid";
        $this->_headerText = Mage::helper("kid")->__("Rodzaj Manager");
        $this->_addButtonLabel = Mage::helper("kid")->__("Dodaj rodzaj");
        parent::__construct();
    }

}