<?php

require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/class_przelewy24.php';

class Dialcom_Przelewy_Model_Config_Kanaly
{

    /** @var int */
    private $storeID;

    /**
     * Dialcom_Przelewy_Model_Config_Kanaly constructor.
     */

    public static function getChannelsRaty()
    {
        return array(72, 129, 136);
    }

    public static function getChannelsNonPln()
    {
        return array(124, 140, 145, 152, 66, 92);
    }

    private static function getWsdlService($merchantId)
    {
        return str_replace('[P24_MERCHANT_ID]', (int) $merchantId, 'external/[P24_MERCHANT_ID].wsdl');
    }

    private static function getWsdlCCService()
    {
        return 'external/wsdl/charge_card_service.php?wsdl';
    }

    private static function soap_method_exists($soapClient, $method)
    {
        $list = $soapClient->__getFunctions();
        if (is_array($list)) {
            foreach ($list as $line) {
                list($type, $name) = explode(' ', $line, 2);
                if (strpos($name, $method) === 0) {
                    return true;
                }
            }
        }
        return false;
    }


    public static function runIvrPayment($order)
    {
        $storeID = Mage::helper("przelewy")->getStoreID();

        $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($order->getOrderCurrencyCode());

        $P24C = new Przelewy24Class($fullConfig['merchant_id'], $fullConfig['shop_id'], $fullConfig['salt'],
            Mage::getStoreConfig('payment/dialcom_przelewy/mode', $storeID) == '1');

        try {
            $s = new SoapClient($P24C->getHost() . self::getWsdlService($fullConfig['merchant_id']),
                array('trace' => true, 'exceptions' => true));
            if (self::soap_method_exists($s, 'TransactionMotoCallBackRegister')) {

                // usunięcie prefiksu kraju
                $clientPhone = $order->getShippingAddress()->getTelephone();
                if (strpos($clientPhone, '+48') === 0) {
                    $clientPhone = substr($clientPhone, 3);
                } elseif (strpos($clientPhone, '0048') === 0) {
                    $clientPhone = substr($clientPhone, 4);
                } elseif (strpos($clientPhone, '48') === 0) {
                    $clientPhone = substr($clientPhone, 2);
                }

                $sessionId = $order->getIncrementId() . '|' . md5(uniqid(mt_rand(), true) . ':' . microtime(true)) . '|' . $storeID;

                $res = $s->__call('TransactionMotoCallBackRegister', array(
                    'login' => $fullConfig['merchant_id'],
                    'pass' => $fullConfig['api'],
                    'details' => array(
                        'clientPhone' => addslashes($clientPhone),
                        'amount' => number_format($order->getGrandTotal() * 100, 0, "", ""),
                        'currency' => addslashes($order->getOrderCurrencyCode()),
                        'paymentId' => (int)$order->getIncrementId(),
                        'description' => 'Zamówienie ' . (int)$order->getIncrementId(),
                        'sessionId' => substr($sessionId,0,100),
                        'clientEmail' => filter_var($order->getCustomerEmail(), FILTER_SANITIZE_EMAIL),
                        'merchantEmail' => filter_var(Mage::getStoreConfig('trans_email/ident_general/email', $storeID), FILTER_SANITIZE_EMAIL),
                        'client' => addslashes($order->getBillingAddress()->getData('firstname') . ' ' . $order->getBillingAddress()->getData('lastname')),
                        'urlStatus' => Mage::getUrl('przelewy/przelewy/status'),
                        'typeOfResponse' => 'post',
                        'sendEmail' => 0,
                        'time' => 0,
                        'additionalInfo' => '',
                    )
                ));
                if ($res->error->errorCode > 0) {
                    error_log(__METHOD__ . ' ' . $res->error->errorMessage);
                }

                $order->setData('p24_session_id', $sessionId);
                $order->save();
            }
        } catch (Exception $e) {
            error_log(__METHOD__ . ' ' . $e->getMessage());
        }

    }

    public function toOptionArray($currency = 'PLN')
    {
        $payment_list = array();
        $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($currency);
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        $P24C = new Przelewy24Class($fullConfig['merchant_id'], $fullConfig['shop_id'], $fullConfig['salt'],
            Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID) == '1');

        try {
            $s = new SoapClient($P24C->getHost() . $this->getWsdlService($fullConfig['merchant_id']),
                array('trace' => true, 'exceptions' => true, 'cache_wsdl' => WSDL_CACHE_NONE));
            $res = $s->PaymentMethods($fullConfig['shop_id'], $fullConfig['api'],
                substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2));
        } catch (Exception $e) {
            error_log(__METHOD__ . ' ' . $e->getMessage());
        }
        if (isset($res) && $res->error->errorCode === 0) {
            foreach ($res->result as $item) {
                $payment_list[] = array('value' => $item->id, 'label' => $item->name);
            }
        }
        return $payment_list;
    }
}
