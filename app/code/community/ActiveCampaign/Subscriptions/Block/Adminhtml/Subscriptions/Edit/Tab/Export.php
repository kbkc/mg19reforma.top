<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions_Edit_Tab_Export extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('subscriptions_form', array('legend' => Mage::helper('subscriptions')->__('Export Newsletter Contacts To ActiveCampaign')));

        // gets all customers that are subscribed to the newsletter
        $subscribers = Mage::getResourceModel('newsletter/subscriber_collection')->showStoreInfo()->showCustomerInfo()->getData();

        $connection = Mage::registry('subscriptions_data')->getData();

        if ($connection) {
            $api_url = $connection["api_url"];
            $api_key = $connection["api_key"];

            new ActiveCampaign($api_url, $api_key);
        }

        $fieldset->addField(
            'export_note', 'note', array(
                'text' => Mage::helper('subscriptions')->__('Check the box below, then click the Save Connection button to export ' . count($subscribers) . ' subscribers to ActiveCampaign.'),
            )
        );

        $fieldset->addField(
            'export_confirm', 'checkbox', array(
                'label' => Mage::helper('subscriptions')->__('Confirm?'),
                'name' => 'export_confirm',
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getSubscriptionsData()) {
            $data = Mage::getSingleton('adminhtml/session')->getSubscriptionsData();
            $data["export_confirm"] = 1;
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setSubscriptionsData(null);
        } elseif (Mage::registry('subscriptions_data')) {
            $data = Mage::registry('subscriptions_data')->getData();
            $data["export_confirm"] = 1;
            $form->setValues($data);
        }

        return parent::_prepareForm();
    }
}
