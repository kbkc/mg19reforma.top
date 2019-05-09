<?php

class Dialcom_ZenCard_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);

        $lineBlock = array(
            'lines' => array(),
            'height' => 15
        );

        $count = 0;
        foreach ($totals as $total) {
            $count++;
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = array(
                        array(
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                        array(
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                    );
                }
            }
            if ($count + 1 === count($totals)) {
                $amount = $order->getData('discount_total');
                if ($amount != 0) {
                    $lineBlock['lines'][] = array(
                        array(
                            'text' => Mage::helper('przelewy')->__('ZenCard Discount'),
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => 10,
                            'font' => 'bold'
                        ),
                        array(
                            'text' => Mage::helper('core')->currency($amount, true, false),
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => 10,
                            'font' => 'bold'
                        ),
                    );
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
}
