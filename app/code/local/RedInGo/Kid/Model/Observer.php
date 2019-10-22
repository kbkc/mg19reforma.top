<?php
class RedInGo_Kid_Model_Observer
{

	public function faktury(Varien_Event_Observer $observer)
	{

		if ($observer->getBlock() instanceof Mage_Sales_Block_Order_Recent) {
				$observer->getBlock()->setTemplate('redingo/recent.phtml');
			}
			// frontend/rwd/default/template/sales/order/recent.phtml

	}

}
