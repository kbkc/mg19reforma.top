<?php
	
class RedInGo_Kid_Block_Adminhtml_Grupa_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {

        parent::__construct();
        $this->_objectId = "id";
        $this->_blockGroup = "kid";
        $this->_controller = "adminhtml_grupa";
        $this->_updateButton("save", "label", Mage::helper("kid")->__("Zapis grupe"));
        $this->_updateButton("delete", "label", Mage::helper("kid")->__("Usuń grupe"));

        $this->_addButton("saveandcontinue", array(
                "label"     => Mage::helper("kid")->__("Save And Continue Edit"),
                "onclick"   => "saveAndContinueEdit()",
                "class"     => "save",
        ), -100);

        $this->_addButton("grupa", array(
            //    "label"     => Mage::helper("kid")->__(  $this->getUrl('*/*/product_grupa', array('id' => $this->getRequest()->getParam('id') ) ) ),
                "label" => "Grupa",
            "onclick"   => "grupa()",
                "class"     => "scalable back",
        ), 0);


        $this->_formScripts[] = "
                                function saveAndContinueEdit(){
                                        editForm.submit($('edit_form').action+'back/edit/');
                                }
                        ";
        $this->_formScripts[] = "
            function grupa(){
                editForm.submit('".Mage::helper("adminhtml")->getUrl('admin_kid/adminhtml_grupa/massProduct/ids/' . $this->getRequest()->getParam('id') )."');
            }";
    }

    public function getHeaderText()
    {
        if( Mage::registry("grupa_data") && Mage::registry("grupa_data")->getId() ){
            return Mage::helper("kid")->__("Edycja grupy '%s'", $this->htmlEscape(Mage::registry("grupa_data")->getGropa()));
        } 
        else{
             return Mage::helper("kid")->__("Dodanie grupy");
        }
    }
}