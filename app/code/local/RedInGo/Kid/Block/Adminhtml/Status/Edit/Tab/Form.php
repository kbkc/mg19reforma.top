<?php
class RedInGo_Kid_Block_Adminhtml_Status_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("kid_form", array("legend"=>Mage::helper("kid")->__("Item information")));

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            )
        );

        $fieldset->addField("kid_status", "text", array(
            "label" => Mage::helper("kid")->__("kid_status"),
            "name" => "kid_status",
        ));

        $fieldset->addField("wyslij", "select", array(
          "label" => Mage::helper("kid")->__("wyslij"),
          "name" => "wyslij",
          'values' => $yesno,
        ));

        $fieldset->addField("faktura", "select", array(
          "label" => Mage::helper("kid")->__("faktura"),
          "name" => "faktura",
          'values' => $yesno,
        ));

         $fieldset->addField("dostawa", "select", array(
          "label" => Mage::helper("kid")->__("dostawa"),
          "name" => "dostawa",
          'values' => $yesno,
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('kid')->__('status'),
            'values'   => RedInGo_Kid_Block_Adminhtml_Status_Grid::getValueArray1(),
            'name' => 'status',
        ));

        $fieldset->addField('opis', 'textarea', array(
                  'label'     => Mage::helper('kid')->__('Opis'),
                  'name'      => 'opis',
                  'after_element_html' => '<small> Opis </small>',
                ));

        if (Mage::getSingleton("adminhtml/session")->getStatusData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getStatusData());
            Mage::getSingleton("adminhtml/session")->setStatusData(null);
        }
        elseif(Mage::registry("status_data")) {
            $form->setValues(Mage::registry("status_data")->getData());
        }
        return parent::_prepareForm();
    }
}
