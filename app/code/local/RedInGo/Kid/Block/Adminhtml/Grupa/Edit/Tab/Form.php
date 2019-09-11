<?php
class RedInGo_Kid_Block_Adminhtml_Grupa_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("kid_form", array("legend"=>Mage::helper("kid")->__("Item information")));

        $fieldset->addField("gropa", "select", array(
            "label" => Mage::helper("kid")->__( 'gropa' ),
            "name" => "gropa",
            'values'   => RedInGo_Kid_Model_Select_Rodzaj::toOptionArray(),
            'required' => 'true',
        ));
        
        $fieldset->addField("websites", "select", array(
            "label" => Mage::helper("kid")->__( 'websites' ),
            "name" => "websites",
            'values'   => RedInGo_Kid_Model_Select_Websites::toOptionArray(),
            'required' => 'true',
        ));
        
        $fieldset->addField("type", "select", array(
            "label" => Mage::helper("kid")->__( 'Typ' ),
            "name" => "type",
            'values'   => RedInGo_Kid_Model_Select_Typ::toOptionArray(),
            'required' => 'true',
        ));
        
        $fieldset->addField("category_ids", "select", array(
            "label" => Mage::helper("kid")->__( 'Kategoria' ),
            "name" => "category_ids",
            'values'   => RedInGo_Kid_Model_Select_Kat::toOptionArray(),
        ));
        
        for ($i = 2; $i < 11; $i++) {
            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(4, Mage::getStoreConfig('kid/pole/p'.$i) );
            $fieldset->addField("p".$i, "select", array(
                "label" => Mage::helper("kid")->__( $attributeModel->getData('frontend_label') ),
                "name" => "p".$i,
                "values" =>Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            ));
        }

        if (Mage::getSingleton("adminhtml/session")->getGrupaData())
        {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getGrupaData());
            Mage::getSingleton("adminhtml/session")->setGrupaData(null);
        } 
        elseif(Mage::registry("grupa_data")) {
            $form->setValues(Mage::registry("grupa_data")->getData());
        }
        return parent::_prepareForm();
    }
}
