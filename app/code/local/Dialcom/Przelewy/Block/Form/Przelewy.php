<?php

require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/shared-libraries/autoloader.php';

class Dialcom_Przelewy_Block_Form_Przelewy extends Mage_Payment_Block_Form
{
    /** @var int */
    private $storeID;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dialcom/przelewy/form.phtml');
        $this->storeID = Mage::helper("przelewy")->getStoreID();
    }

    public function getCards()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            return Mage::getModel('recuring/recuring')->getCards($customerData->getId());
        }
        return array();
    }

    public function getDescription()
    {
        return Mage::getStoreConfig('payment/dialcom_przelewy/text', $this->storeID);
    }

    public function getLastPaymentMethod()
    {
        try {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $order = Mage::getModel('sales/order')->getCollection()
                    ->addFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                    ->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC)
                    ->getFirstItem();
                if ($order && $order->getPayment()) {
                    $paymentData = $order->getPayment()->getData();
                    if (isset($paymentData['additional_information']['method_id'])) {
                        $lastMethod = $paymentData['additional_information']['method_id'];
                        if (!in_array($lastMethod, Dialcom_Przelewy_Model_Recuring::getChannelsCards())) {
                            return $lastMethod;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            error_log(__METHOD__ . ' ' . $e->getMessage());
        }
        return false;
    }

    public function getKanalyPlatnosci()
    {
        $kanaly = new Dialcom_Przelewy_Model_Config_Kanaly();
        $currency = strtoupper(Mage::app()->getStore()->getCurrentCurrencyCode());
        $nonPln = $kanaly->getChannelsNonPln();
        $payment_list = array();
        foreach ($kanaly->toOptionArray($currency) as $item) {
            if ($currency == 'PLN' || in_array($item['value'], $nonPln)) {
                $payment_list[$item['value']] = $item['label'];
            }
        }
        return $payment_list;
    }

    public function replaceSlashes($value) {
        return preg_replace('/"|<|>/', '', $value);
    }

    public function getBankHtml($bank_id, $bank_name, $text = '', $cc_id = '', $class = '')
    {
        $bank_id = (int)$bank_id;
        $bank_name = filter_var($bank_name, FILTER_SANITIZE_STRING);
        $text = filter_var($text, FILTER_SANITIZE_STRING);
        $cc_id = (int)$cc_id;
        $class = filter_var($class,FILTER_SANITIZE_STRING);
        return '<a class="bank-box ' . $class . '" data-id="' . $bank_id . '" data-cc="' . $cc_id . '">' .
        '<div class="bank-logo bank-logo-' . $bank_id . '">' .
        (empty($text) ? "" : "<span>{$text}</span>") .
        '</div><div class="bank-name">' . $bank_name . '</div></a>';
    }

    public function getBankTxt($bank_id, $bank_name, $checked = false, $cc_id = '', $text = '')
    {
        $bank_id = (int)$bank_id;
        $bank_name = filter_var($bank_name, FILTER_SANITIZE_STRING);
        $text = filter_var($text, FILTER_SANITIZE_STRING);
        $cc_id = (int)$cc_id;
        $checked = boolval($checked);
        return
            '<li><div class="input-box  bank-item">' .
            '<input id="przelewy_method_id_' . $bank_id . '-' . $cc_id . '" name="payment_method_id" data-id="' . $bank_id . '" data-cc="' . $cc_id . '" data-text="' . $text . '" ' .
            ' class="radio" type="radio" ' . ($checked ? 'checked="checked"' : '') . ' />' .
            '<label for="przelewy_method_id_' . $bank_id . '-' . $cc_id . '">' . $bank_name . '</label>' .
            '</div></li>';

    }

    public function p24getCssUrl()
    {
        list($proto, $p24CssUrl) = explode("://",
            Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'skin/adminhtml/default/default/dialcom/przelewy/',
            2);
        $p24CssUrl = "//" . $p24CssUrl;
        return $p24CssUrl;
    }

    public function p24getJsUrl()
    {
        list($proto, $p24JsUrl) = explode("://",
            Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'js/dialcom/przelewy/', 2);
        $p24JsUrl = "//" . $p24JsUrl;
        return $p24JsUrl;
    }
}
