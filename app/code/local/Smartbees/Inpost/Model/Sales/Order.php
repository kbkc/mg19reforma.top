<?php
class Smartbees_Inpost_Model_Sales_Order extends Mage_Sales_Model_Order{
	public function getShippingDescription(){
		$desc = parent::getShippingDescription();
		$pickupObject = $this->getPickupObject();
		if($pickupObject){
			$desc .= $pickupObject->getStore();
			$desc .= $pickupObject->getName();
			$desc .= '';
		}
		return $desc;
	}
}