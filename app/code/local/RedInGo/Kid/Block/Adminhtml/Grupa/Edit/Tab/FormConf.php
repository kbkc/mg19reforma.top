<?php
class RedInGo_Kid_Block_Adminhtml_Grupa_Edit_Tab_FormConf extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("kid_form", array("legend"=>Mage::helper("kid")->__("Item information")));


        
        for ($i = 2; $i < 10; $i++) {
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
