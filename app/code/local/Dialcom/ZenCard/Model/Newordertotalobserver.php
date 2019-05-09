<?php

require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/shared-libraries/autoloader.php';

class Dialcom_ZenCard_Model_Newordertotalobserver
{

    /** @var int */
    private $storeID;

    public function saveDiscountTotal(Varien_Event_Observer $observer)
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        if ((int)Mage::getStoreConfig('przelewytab1/additionall/zencard', $this->storeID) === 1) {
            $result = $this->processZenCard($observer);
            if (!$result) {
                $this->displayZenCardError();
            }
        }
    }

    public function processZenCard($observer)
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        try {
            $merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID);
            $apiKey = Mage::getStoreConfig('przelewytab1/klucze/api_key', $this->storeID);
            $zenCardApi = new ZenCardApi($merchantId, $apiKey);

            $order = $observer->getEvent()->getOrder();
            $quote = $observer->getEvent()->getQuote();
            $email = $this->getZenCardEmail();
            $amount = $this->getZenCardAmount($quote);
            $zenCardOrderId = $this->getZenCardOrderId($zenCardApi, $quote);

            $transaction = $zenCardApi->verify($email, $amount, $zenCardOrderId);
            Mage::log($transaction, null, 'zencard-verify.log');

            if ($transaction->isVerified()) {
                $transactionConfirm = $zenCardApi->confirm($zenCardOrderId, $amount);
                Mage::log($transactionConfirm, null, 'zencard-confirm.log');
                if ($transactionConfirm->isConfirmed()) {
                    return $this->zenCardOrderProcessing($order, $transactionConfirm, $quote);
                } else {
                    return true;
                }
            }
        } catch (\Exception $ex) {
            Mage::log(__METHOD__ . ' ' . $ex->getMessage());
        }

        return false;
    }

    public function zenCardOrderProcessing($order, $transactionConfirm, $quote)
    {
        if ($transactionConfirm->withZencard()) {
            $info = $transactionConfirm->getInfo();

            $amount = $transactionConfirm->getAmountWithDiscount();
            $without = $transactionConfirm->getAmount();
            $discountAmount = round($without - $amount);

            $this->addOrderDiscount($order, $discountAmount / 100, $info, $quote);
            $this->addAddressDiscount($discountAmount / 100, $quote);
        }

        return true;
    }

    public function addOrderDiscount($order, $amount, $info, $quote)
    {
        $order->addStatusHistoryComment($info ? $info : 'Rabat ZenCard');

        $discount = $quote->getStore()->roundPrice($amount);

        $grandTotal = $order->getGrandTotal();
        $baseGrandTotal = $order->getBaseGrandTotal();

        $order->setGrandTotal($grandTotal - abs($discount));
        $order->setBaseGrandTotal($baseGrandTotal - abs($discount));
        $order->setData('discount_total', $discount);

        $order->save();
        $quote->save();
    }

    public function addAddressDiscount($amount, $quote)
    {
        $address = $this->getZenCardAddress($quote);
        $address->setData('discount_total', $amount);
        $address->save();
        $quote->save();
    }

    public function getZenCardEmail()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $email = $customer->getEmail();
        } else {
            $milliseconds = round(microtime(true) * 1000);
            $email = 'przelewy_' . $milliseconds . '@zencard.pl';
        }

        return $email;
    }

    public function getZenCardAmount($quote)
    {
        $quoteItems = $quote->getAllItems();
        $productsWithTaxes = 0;
        foreach ($quoteItems as $item) {
            $productsWithTaxes += $item->getRowTotalInclTax();
        }

        return $productsWithTaxes * 100;
    }

    public function getZenCardOrderId($zenCardApi, $quote)
    {
        $address = $this->getZenCardAddress($quote);
        $storeUrl = Mage::getBaseUrl();
        $orderId = $address->getQuote()->getId();

        return $zenCardApi->buildZenCardOrderId($orderId, $storeUrl);
    }

    public function getZenCardAddress($quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $billingAddress = $quote->getBillingAddress();

        return $shippingAddress ? $shippingAddress : $billingAddress;
    }

    public function displayZenCardError()
    {
        $alertText = "<strong>" . __("Error during processing ZenCard.") . "</strong><br/>" . __("Discount will be removed from the order.") . "<br/>" . __("You will be redirected to a cart page in few seconds.");
        $result['success'] = false;
        $result['error'] = true;

        $alertBackgrounStyle = 'position: fixed; top: 0; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.1); display: none;';
        $alertContainerStyle = 'position: absolute; top: 20%; text-align: center; width: 100%;';
        $alertStyle = 'background-color: #f2dede; color: #a94442; border: 2px dashed #ebccd1; padding: 30px 80px; border-radius: 5px; display: inline-block;';

        $result['update_section']['html'] =
            '<script>' .
            'jQuery("body").append("<div id=\'zenCardErrorAlert\' style=\'' . $alertBackgrounStyle . '\'><div style=\'' . $alertContainerStyle . '\'><div style=\'' . $alertStyle . '\'>' . $alertText . '</div></div></div>");' .
            'setTimeout(function() { jQuery("#zenCardErrorAlert").fadeIn(); }, 100);' .
            'setTimeout(function() { jQuery("#zenCardErrorAlert").fadeOut( "slow", function() { window.location.href=\'' . Mage::getUrl('') . '\' }); }, 8000);' .
            '</script>';
        $result['update_section']['name'] = 'review';

        $response = Mage::app()->getResponse();
        $response->setBody(Mage::helper('core')->jsonEncode($result));
        $response->sendResponse();
        exit;
    }

    public function saveDiscountTotalForMultishipping(Varien_Event_Observer $observer)
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        if ((int)Mage::getStoreConfig('przelewytab1/additionall/zencard', $this->storeID) === 1) {
            $order = $observer->getEvent()->getOrder();
            $quote = $observer->getEvent()->getQuote();

            $address = $observer->getEvent()->getAddress();
            $zenCardDiscount = $this->getZenCardDiscount($address);

            if ($zenCardDiscount !== false) {
                $discount = $quote->getStore()->roundPrice($zenCardDiscount);
                $grandTotal = $order->getGrandTotal();
                $baseGrandTotal = $order->getBaseGrandTotal();

                $order->setGrandTotal($grandTotal - abs($discount));
                $order->setBaseGrandTotal($baseGrandTotal - abs($discount));

                $order->setData('discount_total', $discount);
                $address->setData('discount_total', $discount);

                $address->save();
                $quote->save();
                $order->save();
            }

            $zenCardConfirmed = Dialcom_Przelewy_Model_Payment_Przelewy::confirmZenCardDiscount($order);

            if (!$zenCardConfirmed && $zenCardDiscount) {
                $order->addStatusHistoryComment('Próba potwierdzenia kuponu ZenCard zakończona niepowodzeniem!');
                $order->save();
            }

            if ($zenCardDiscount === false) {
                $this->displayZenCardError();
            }
        }
    }
}