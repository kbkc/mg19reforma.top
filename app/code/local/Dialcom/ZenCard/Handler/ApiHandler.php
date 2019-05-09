<?php

require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/shared-libraries/autoloader.php';

class ApiHandler
{

    /** @var int */
    private $storeID;

    /**
     * @return string
     */
    public function getZendCardScript()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();
        $merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID);
        $apiKey = Mage::getStoreConfig('przelewytab1/klucze/api_key', $this->storeID);

        $zenCardApi = new ZenCardApi($merchantId, $apiKey);
        return $zenCardApi->getScript();
    }

    /**
     * @return bool
     */
    public function isZendCardEnabled()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();
        $merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID);
        $apiKey = Mage::getStoreConfig('przelewytab1/klucze/api_key', $this->storeID);

        $zenCardApi = new ZenCardApi($merchantId, $apiKey);
        return $zenCardApi->isEnabled();
    }

    /**
     * @return string
     */
    public function getZendCardScriptUrl()
    {
        $url = '';
        try {
            $script = $this->getZendCardScript();
            $dom = new \DOMDocument();
            @$dom->loadHTML($script);
            $elements = $dom->getElementsByTagName('script');
            foreach ($elements as $element) {
                $url = $element->getAttribute('src');
            }
        } catch (\Exception $ex) {
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getZendCardScriptToken()
    {
        $token = '';
        try {
            $script = $this->getZendCardScript();
            $dom = new \DOMDocument();
            @$dom->loadHTML($script);
            $elements = $dom->getElementsByTagName('script');
            foreach ($elements as $element) {
                $token = $element->getAttribute('data-zencard-mtoken');
            }
        } catch (\Exception $ex) {
        }

        return $token;
    }
}
