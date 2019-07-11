<?php 
/* We will redirect to the checkout/cart for exemple */
class Smartbees_Redirect_Model_Observer extends Varien_Event_Observer {
    public function noRoutUrl($observer) {
        if (strpos(Mage::helper('core/url')->getCurrentUrl(),'quickorder') != false && !Mage::getSingleton('customer/session')->isLoggedIn())  {
            Mage::app()->getResponse()->setRedirect('/no-route');
        }
    } 
}
?>