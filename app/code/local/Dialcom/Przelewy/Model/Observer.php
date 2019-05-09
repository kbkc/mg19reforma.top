<?php

class Dialcom_Przelewy_Model_Observer
{

    /** @var int */
    private $storeID;

    private function getPaymentEmailUrl($order_id)
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/przelewy/paymentemail',
            array('order_id' => (int)$order_id));
    }

    private function getPaymentIVRUrl($order_id)
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/przelewy/paymentivr', array('order_id' => (int)$order_id));
    }

    public function adminhtmlWidgetContainerHtmlBefore(Varien_Event_Observer $observer)
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == "Mage_Adminhtml_Block_Sales_Order_View") {
            $order_id = (int) Mage::app()->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($order_id);
            if ($order && $order->getBaseTotalDue() > 0) { // jeśli jest coś jeszcze do zapłacenia to pokaż przyciski
                $templateId = (int)Mage::getStoreConfig('przelewytab1/paysettings/sendlink_mailtemplate',
                    $this->storeID);
                if (!$order->isCanceled()) {
                    $message = Mage::helper('przelewy')->__('Are you sure you want to send the customer an e-mail link and start the payment process with Przelewy24?');
                    $block->addButton('p24_link', array(
                        'label' => Mage::helper('przelewy')->__('Send an e-mail with the link for P24'),
                        'onclick' => 'confirmSetLocation(\'' . $message . '\', \'' . $this->getPaymentEmailUrl($order_id) . '\')',
                    ));
                }
                $ivr = (int)Mage::getStoreConfig('przelewytab1/paysettings/ivr', $this->storeID);
                if (!$order->isCanceled() && $ivr) {
                    $message = Mage::helper('przelewy')->__('Are you sure you want to start the payment process by Przelewy24 IVR and call the customer back?');
                    $block->addButton('p24_ivr', array(
                        'label' => Mage::helper('przelewy')->__('IVR payment with P24'),
                        'onclick' => 'confirmSetLocation(\'' . $message . '\', \'' . $this->getPaymentIVRUrl($order_id) . '\')',
                    ));
                }
            }
        }
    }
}