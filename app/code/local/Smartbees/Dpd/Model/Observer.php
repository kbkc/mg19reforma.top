<?php 
/* We will redirect to the checkout/cart for exemple */
class Smartbees_Dpd_Model_Observer extends Varien_Event_Observer {
    public function send_email($observer) {
        $event = $observer->getEvent();
        $track = $event->getTrack();
        $shipment = $track->getShipment();
        $order = $shipment->getOrder();

        $customer = $order->getCustomerName();
        $email= $order->getCustomerEmail();
        if(($order->getShippingMethod()=="smartbees_smartbees")||($order->getShippingMethod()=="smartbees_express")) 
        {
            if(strpos($order->getStoreName(), 'Polska')) $templateId = Mage::getStoreConfig('carriers/smartbees/chooseemailtemplatepl');
            else $templateId = Mage::getStoreConfig('carriers/smartbees/chooseemailtemplateeu');
        }
        elseif($order->getShippingMethod()=="pickup_pickup") 
        {
            if(strpos($order->getStoreName(), 'Polska')) $templateId = Mage::getStoreConfig('carriers/pickup/chooseemailtemplatepl');
            else $templateId = Mage::getStoreConfig('carriers/pickup/chooseemailtemplateeu');
        }
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName,
            'email' => $senderEmail);

        $storeId = Mage::app()->getStore()->getId();

        $vars = array('customerEmail' => $email,
            'customerName' => $customer,
            'trackingNr' => $track->getNumber(),
            'orderId' => $order->getIncrementId(),
            'shippingMethod' => $order->getShippingDescription(),
            'storeName' => Mage::app()->getStore()->getFrontendName()
        );
        
        $translate  = Mage::getSingleton('core/translate');
        
        Mage::getModel('core/email_template')
        ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
        ->sendTransactional($templateId, $sender,  $email, $customer, $vars, $storeId);

        $translate->setTranslateInline(true);
    } 
}
?>