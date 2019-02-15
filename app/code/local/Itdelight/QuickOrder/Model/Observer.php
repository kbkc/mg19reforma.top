<?php

class Itdelight_QuickOrder_Model_Observer
{
    public function closeUrl()
    {
        return Mage::app()->getResponse()->setRedirect(Mage::getUrl("/"));
    }
}