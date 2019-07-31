<?php

require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/shared-libraries/autoloader.php';

class Dialcom_ZenCard_Model_Quote_Address_Total_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    /** @var int */
    private $storeID;

    /**
     * Dialcom_ZenCard_Model_Quote_Address_Total_Discount constructor.
     */
    public function __construct()
    {
        $this->setCode('discount_total');
        $this->storeID = Mage::helper("przelewy")->getStoreID();
    }

    /**
     * Collect totals information about discount
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        /*parent:: collect($address);
        if ((int)Mage::getStoreConfig('przelewytab1/additionall/zencard', $this->storeID) !== 1) {
            return $this;
        }

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }
        $quote = $address->getQuote();

        $discountAmount = $this->getZenCardDiscount($address);

        $discountAmount = $quote->getStore()->roundPrice($discountAmount);
        $this->_setAmount($discountAmount)->_setBaseAmount($discountAmount);
        $address->setData('discount_total', $discountAmount);*/

        return $this;
    }

    /**
     * Add discount totals information to address object
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        parent:: fetch($address);
        $amount = $address->getTotalAmount($this->getCode());
        if ($amount != 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $this->getLabel(),
                'value' => $amount
            ));
        }

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage:: helper('zencard')->__('ZenCard Discount');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return float
     */
    private function getZenCardDiscount(Mage_Sales_Model_Quote_Address $address)
    {
        $result = 0.00;
        $merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID);
        $apiKey = Mage::getStoreConfig('przelewytab1/klucze/api_key', $this->storeID);
        $zenCardApi = new ZenCardApi($merchantId, $apiKey);

        try {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $email = $customer->getEmail();
            } else {
                $milliseconds = round(microtime(true) * 1000);
                $email = 'przelewy_' . $milliseconds . '@zencard.pl';
            }

            $amount = $address->getSubtotal() * 100;
            $storeUrl = Mage::getBaseUrl();
            $orderId = $address->getQuote()->getId();
            $zenCardOrderId = $zenCardApi->buildZenCardOrderId($orderId, $storeUrl);

            $transaction = $zenCardApi->verify($email, $amount, $zenCardOrderId);
            Mage::log($transaction, null, 'zencard-verify.log');
            if ($transaction && $transaction->isVerified() && $transaction->hasDiscount()) {
                $result = $transaction->getDiscountAmountNegative();
            }
        } catch (\Exception $ex) {
            Mage::log(__METHOD__ . ' ' . $ex->getMessage());
        }

        return $result;
    }
}