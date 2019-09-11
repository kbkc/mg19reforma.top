<?php
class RedInGo_Kid_Block_Adminhtml_Rodzaj_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("kid_form", array("legend"=>Mage::helper("kid")->__("Item information")));

        
        $fieldset->addField("nazwa", "text", array(
        "label" => Mage::helper("kid")->__("Nazwa"),
        "name" => "nazwa",
        ));
        
        $fieldset->addField("kat", "select", array(
        "label" => Mage::helper("kid")->__("Kategoria"),
        'values'   => RedInGo_Kid_Model_Select_Kat::toOptionArray(),
        "name" => "kat",
        ));
        
        $fieldset->addField("art_set", "select", array(
        "label" => Mage::helper("kid")->__("Zestawamem cech"),
        'values'   => RedInGo_Kid_Model_Select_ArtSet::toOptionArray(),
        "name" => "art_set",
        ));

        if (Mage::getSingleton("adminhtml/session")->getRodzajData())
        {
                $form->setValues(Mage::getSingleton("adminhtml/session")->getRodzajData());
                Mage::getSingleton("adminhtml/session")->setRodzajData(null);
        } 
        elseif(Mage::registry("rodzaj_data")) {
            $form->setValues(Mage::registry("rodzaj_data")->getData());
        }
        return parent::_prepareForm();
    }
}