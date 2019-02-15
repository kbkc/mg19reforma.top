<?php
class Itdelight_SeoRemarks_Block_GoogleTagManager_Order extends Yireo_GoogleTagManager_Block_Order
{
    /**
     * @return string
     */
    public function getLastOrderId()
    {
        $lastOrderId = (string) Mage::getSingleton('core/session')->getLastRealOrderId();
        return $lastOrderId;
    }
}