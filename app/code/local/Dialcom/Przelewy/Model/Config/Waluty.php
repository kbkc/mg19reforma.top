<?php

class Dialcom_Przelewy_Model_Config_Waluty
{
    public function toOptionArray()
    {
        $currencies = array();
        $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
        if (is_array($codes) && count($codes) > 1) {
            $rates = Mage::getModel('directory/currency')->getCurrencyRates(Mage::app()->getStore()->getBaseCurrency(),
                $codes);

            foreach ($codes as $code) {
                if (isset($rates[$code]) && $code != 'PLN') {
                    $currencies[$code] = Mage::app()->getLocale()->getTranslation($code, 'nametocurrency');
                }
            }
        }
        return $currencies;
    }

    public static function multicurrGetConfig($name, $currency = null, $default = null)
    {
        $result = array();
        $storeID = Mage::helper("przelewy")->getStoreID();

        $vals = explode(',', Mage::getStoreConfig('przelewytab1/multicurr/multicurr_' . $name, $storeID));
        if (is_array($vals)) {
            foreach ($vals as $item) {
                $items = explode(':', $item);
                if (is_array($items) && count($items) == 2) {
                    $result[$items[0]] = $items[1];
                }
            }
        }
        if (!is_null($currency)) {
            if (isset($result[$currency]) && !empty($result[$currency])) {
                return $result[$currency];
            }
            return $default;
        }
        return $result;
    }

    public static function getFullConfig($currency, $storeID = 0)
    {
        if(!$storeID) {
            $storeID = Mage::helper("przelewy")->getStoreID();
        }

        $merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $storeID);
        $shopId = Mage::getStoreConfig('payment/dialcom_przelewy/shop_id', $storeID);
        $salt = Mage::getStoreConfig('payment/dialcom_przelewy/salt', $storeID);
        $api = Mage::getStoreConfig('przelewytab1/klucze/api_key', $storeID);

        if (!empty($currency) && $currency != 'PLN') {
            $merchantId = self::multicurrGetConfig('merchantid', $currency, $merchantId);
            $shopId = self::multicurrGetConfig('shopid', $currency, $shopId);
            $salt = self::multicurrGetConfig('salt', $currency, $salt);
            $api = self::multicurrGetConfig('api', $currency, $api);
        }
        $ret = array(
            'merchant_id' => $merchantId,
            'shop_id' => $shopId,
            'salt' => $salt,
            'api' => $api
        );
        return $ret;
    }

}
