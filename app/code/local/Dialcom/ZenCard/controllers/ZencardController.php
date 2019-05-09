<?php

class Dialcom_Zencard_ZencardController extends Mage_Core_Controller_Front_Action
{
    function getProductsWithTaxesAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quoteItems = $quote->getAllItems();
        $productsWithTaxes = 0;
        foreach ($quoteItems as $item) {
            $productsWithTaxes += $item->getRowTotalInclTax();
        }
        echo $productsWithTaxes;
    }

    function getGrandTotalAction()
    {
        echo Mage::getModel('checkout/cart')->getQuote()->getGrandTotal()
            ? Mage::getModel('checkout/cart')->getQuote()->getGrandTotal()
            : 0;
    }
}
