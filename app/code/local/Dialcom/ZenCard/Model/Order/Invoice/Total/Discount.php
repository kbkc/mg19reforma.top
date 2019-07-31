<?php

class Dialcom_ZenCard_Model_Order_Invoice_Total_Discount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $orderDiscountTotal = $order->getDiscountTotal();
        if ($orderDiscountTotal && count($order->getInvoiceCollection()) == 0) {
            $invoice->setGrandTotal($invoice->getGrandTotal() - $orderDiscountTotal);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $orderDiscountTotal);
        }
        return $this;
    }
}