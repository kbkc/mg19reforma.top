<?php
class RedInGo_Kid_Block_Adminhtml_Rodzaj_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()    {
        parent::__construct();
        $this->setId("rodzaj_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("kid")->__("Item Information"));
    }
    protected function _beforeToHtml()    {
        $this->addTab("form_section", array(
            "label" => Mage::helper("kid")->__("Item Information"),
            "title" => Mage::helper("kid")->__("Item Information"),
            "content" => $this->getLayout()->createBlock("kid/adminhtml_rodzaj_edit_tab_form")->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}
