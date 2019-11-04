<?php

class Smartbees_CronShipmentTrack_Model_Observer
{
    /**
     * Import notifications from Ultra
     */
    public function sendemail()
    {
		$emails = Mage::getModel("cronshipmenttrack/cronshipmenttrack")->getCollection()->addFieldToFilter('flag', array('eq' => 0))->getItems();
        if($emails){
            foreach($emails as $em){
                Zend_Debug::dump($em->getData());
                $order = Mage::getModel('sales/order')->load($em->getData()['order_id']);
                $track_id = $em->getData()['tracking_id'];
                Zend_Debug::dump($em->getData()['tracking_id']);
               // send_email($order, $track_id);
                //$email->setData('flag', 1);  // or $item->setCustomField($value);
                //$email->save();

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
                    'trackingNr' =>  $track_id,
                    'orderId' => $order->getIncrementId(),
                    'shippingMethod' => $order->getData()['shipping_description'],
                    'storeName' => Mage::app()->getStore()->getFrontendName()
                );
                $translate  = Mage::getSingleton('core/translate');

                try {
                    Mage::getModel('core/email_template')
                    ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                    ->sendTransactional($templateId, $sender,  $email, $customer, $vars, $storeId);
                    $translate->setTranslateInline(true);

                    $em->setData('flag', 1);
                    $em->save();

                } catch(Exception $error) {
                    Mage::log($error->getMessage());
                }
            }
        }
       // $order = Mage::getModel('sales/order')->loadByIncrementId('2000000028');
      // Zend_Debug::dump("koniec");die;
        //Mage::log("TEST success", null, "dev.log");
    }
	
	
}