<?php
class RedInGo_Kid_Block_Adminhtml_Grupa_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
        public function __construct()
        {
            parent::__construct();
            $this->setId("grupa_tabs");
            $this->setDestElementId("edit_form");
            $this->setTitle(Mage::helper("kid")->__("Informacje"));
            

        }
        protected function _beforeToHtml()
        {
            $this->addTab("form_section", array(
            "label" => Mage::helper("kid")->__("Ustawienia"),
            "title" => Mage::helper("kid")->__("Ustawienia"),
            "content" => $this->getLayout()->createBlock("kid/adminhtml_grupa_edit_tab_form")->toHtml(),
            ));
            
            $this->addTab("form_section_conf", array(
            "label" => Mage::helper("kid")->__("Ustawienia dodatkowe"),
            "title" => Mage::helper("kid")->__("Ustawienia dodatkowe"),
            "content" => $this->getLayout()->createBlock("kid/adminhtml_grupa_edit_tab_conf")->toHtml(),
            ));
            
            return parent::_beforeToHtml();
        }

}
