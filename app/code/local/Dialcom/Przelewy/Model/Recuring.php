<?php

/**
 * Class Dialcom_Przelewy_Model_Recuring
 */
class Dialcom_Przelewy_Model_Recuring extends Mage_Core_Model_Abstract
{

    /** @var int */
    private $storeID;

    public function _construct()
    {
        $this->_init('recuring/recuring');
        $this->storeID = Mage::helper("przelewy")->getStoreID();
    }

    public static function getChannelsCards()
    {
        return array(140, 142, 145);
    }

    public static function getWsdlCCService()
    {
        return 'external/wsdl/charge_card_service.php?wsdl';
    }

    static public function getCards($customer_id = null)
    {
        $customer_id = (int)$customer_id;
        if (0 >= $customer_id) {
            $customer_id = (int) Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        return Mage::getModel('recuring/recuring')->getCollection()->AddFieldToFilter('customer',
            array('eq' => $customer_id));
    }

    static public function unregisterCard($card_id, $customer_id = null)
    {
        try {
            $customer_id = (int)$customer_id;
            if (0 >= $customer_id) {
                $customer_id = (int) Mage::getSingleton('customer/session')->getCustomer()->getId();
            }
            $cart = Mage::getModel('recuring/recuring')->load($card_id);
            if ((int)$cart->getData('customer') == $customer_id)
            {
                $cart->delete();
            }
        } catch (Exception $e) {
        }
    }

    private function _removeExpiredCards($customer_id)
    {
        $collection = $this->getCards((int)$customer_id);
        foreach ($collection as $card) {
            if (date("ym") > $card->getExpires()) {
                $this->unregisterCard($card->getId(), (int)$customer_id);
            }
        }
    }

    private function registerCard($customer_id, $ref_id, $expires, $mask, $type)
    {
        if (!empty($ref_id) && date('ym') <= $expires) {
            try {
                $this->_removeExpiredCards($customer_id);
                Mage::getModel('recuring/recuring')
                    ->setData(array(
                        'customer' => (int) $customer_id,
                        'reference' => (int) $ref_id,
                        'expires' => $expires,
                        'mask' => $mask,
                        'card_type' => $type,
                    ))
                    ->save();
                $this->_removeExpiredCards($customer_id);
                return true;
            } catch (Exception $e) {
                error_log(__METHOD__ . ' ' . $e->getMessage());
            }
        }
        return false;
    }

    public function saveUsedCard($customer_id, $order_id)
    {
        $customer_id = (int)$customer_id;
        $order_id = (int)$order_id;
        if (0 >= $customer_id || 0 >= $order_id) return;
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $oneclickEnabled = Mage::getStoreConfig('przelewytab1/oneclick/oneclick', $this->storeID) == '1';

        $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($order->getOrderCurrencyCode());

        if ($customer_id > 0 && $oneclickEnabled && strlen($fullConfig['api']) == 32) {

            if (self::getP24Forget($customer_id)) {
                return;
            } // nie zapamiętuj karty

            try {
                $P24C = new Przelewy24Class(
                    (int)$fullConfig['merchant_id'],
                    (int)$fullConfig['shop_id'],
                    filter_var($fullConfig['salt'], FILTER_SANITIZE_STRING),
                    (Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID) == '1')
                );

                $soap = new SoapClient($P24C->getHost() . $this->getWsdlCCService(),
                    array('trace' => true, 'exceptions' => true));
                $res = $soap->GetTransactionReference($fullConfig['merchant_id'], $fullConfig['api'], $order_id);

                if ($res->error->errorCode === 0) {
                    $ref = $res->result->refId;
                    $exp = substr($res->result->cardExp, 2, 2) . substr($res->result->cardExp, 0, 2);
                    $this->registerCard($customer_id, $ref, $exp, $res->result->mask, $res->result->cardType);
                }
            } catch (Exception $e) {
                error_log(__METHOD__ . ' ' . $e->getMessage());
                Mage::log($e->getMessage(), null, 'zencard-confirm.log');
            }
        }

    }

    private function refIdForCardId($card_id)
    {
        try {
            return Mage::getModel('recuring/recuring')->load($card_id)->getReference();
        } catch (Exception $e) {
        }
        return false;
    }

    public function chargeCard($order_id, $card_id)
    {
        $order_id = (int) $order_id;
        $card_id = (int) $card_id;
        if (0 >= $order_id || 0 >= $card_id) return false;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $storeID = Mage::helper("przelewy")->getStoreID();
            $session_id = $order_id . '|' . md5(uniqid(mt_rand(), true) . ':' . microtime(true)) . '|' . $storeID;
            $amount = number_format($order->getGrandTotal() * 100, 0, "", "");
            $currency = $order->getOrderCurrencyCode();
            $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($currency);
            $ref = $this->refIdForCardId($card_id);

            if ($ref == false) {
                return false;
            }
            $P24C = new Przelewy24Class($fullConfig['merchant_id'],
                $fullConfig['shop_id'],
                $fullConfig['salt'],
                (Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID) == '1'));
            try {
                $s = new SoapClient($P24C->getHost() . $this->getWsdlCCService(),
                    array('trace' => true, 'exceptions' => true));
                $res = $s->ChargeCard(
                    $fullConfig['merchant_id'], $fullConfig['api'], $ref, $amount, $currency,
                    filter_var($customer->getEmail(), FILTER_SANITIZE_EMAIL), $session_id, addslashes($customer->getName()), Mage::helper('przelewy')->__('order').' ' . $order_id
                );

                $order->setData('p24_session_id', $session_id);
                $order->save();

                return $res->error->errorCode === 0;
            } catch (Exception $e) {
                error_log(__METHOD__ . ' ' . $e->getMessage());
            }
        }
        return false;
    }

    public static function getP24Forget($customer_id = null)
    {
        if (is_null($customer_id)) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        } else {
            $customer = Mage::getModel('customer/customer')->load((int)$customer_id);
        }
        return (bool)$customer->getData('p24_forget');
    }

    private static function isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if (Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }

    public static function setP24Forget($value)
    {
        if (!self::isAdmin()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            $customer->setData('p24_forget', $value ? '1' : '0');
            $customer->save();
        }
    }
}