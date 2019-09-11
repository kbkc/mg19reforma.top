<?php
	
class RedInGo_Kid_Block_Adminhtml_Status_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {

        parent::__construct();
        $this->_objectId = "id";
        $this->_blockGroup = "status";
        $this->_controller = "adminhtml_status";
        $this->_updateButton("save", "label", Mage::helper("kid")->__("Save Item"));
        $this->_updateButton("delete", "label", Mage::helper("kid")->__("Delete Item"));

        $this->_addButton("saveandcontinue", array(
                "label"     => Mage::helper("kid")->__("Save And Continue Edit"),
                "onclick"   => "saveAndContinueEdit()",
                "class"     => "save",
        ), -100);
     
        $this->_addButton("grupa", array(
                "label"     => Mage::helper("kid")->__("Save And Continue Edit"),
                "onclick"   => "saveAndContinueEdit()",
                "class"     => "save",
        ), -200);
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                    editForm.submit($('edit_form').action+'back/edit/');
            }";
    }

    public function getHeaderText()    {
        if( Mage::registry("status_data") && Mage::registry("status_data")->getId() ){
            return Mage::helper("kid")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("status_data")->getId()));
        } 
        else{
            return Mage::helper("kid")->__("Add Item");
        }
    }
}