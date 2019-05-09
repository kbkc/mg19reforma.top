<?php

require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/class_przelewy24.php';

class Dialcom_Przelewy_Model_Payment_Przelewy extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'dialcom_przelewy';
    protected $_formBlockType = 'przelewy/form_przelewy';
    protected $_infoBlockType = 'przelewy/info_przelewy';

    protected $_isGateway = false;
    protected $_canAuthorize = false;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    protected $_canSaveCc = false;

    /** @var int */
    private $storeID;

    public static function requestGet($url)
    {
        if (strpos($url, 'secure.przelewy24.pl') !== false) {
            $isCurl = function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_close');

            if ($isCurl) {
                $userAgent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
                $curlConnection = curl_init();
                curl_setopt($curlConnection, CURLOPT_URL, $url);
                curl_setopt($curlConnection, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($curlConnection, CURLOPT_USERAGENT, $userAgent);
                curl_setopt($curlConnection, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlConnection, CURLOPT_SSL_VERIFYPEER, false);
                $result = curl_exec($curlConnection);
                curl_close($curlConnection);
                return $result;
            }
        }
            return "";

    }

    public static function getMinRatyAmount()
    {
        return 300;
    }

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();
        $info->setAdditionalInformation('method_id', (int)$data->getMethodId());
        $info->setAdditionalInformation('method_name', addslashes($data->getMethodName()));
        $info->setAdditionalInformation('accept_regulations', $data->getAcceptRegulations());
        $info->setAdditionalInformation('cc_id', (int)$data->getCcId());
        $info->setAdditionalInformation('cc_name', addslashes($data->getCcName()));
        $info->setAdditionalInformation('p24_forget', $data->getP24Forget());

        $p24Forget = $data->getP24Forget();
        if (in_array($p24Forget, array('0', '1')) || $p24Forget === null && Mage::getSingleton('customer/session')->isLoggedIn()) {
            Dialcom_Przelewy_Model_Recuring::setP24Forget($data->getP24Forget() == '1');
        }

        return $this;
    }

    public function getText()
    {
        return $this->getConfigData("text");
    }

    public function getOrderPlaceRedirectUrl()
    {
        $args = Mage::app()->getRequest()->getParam('payment', array());
        if ($args['method'] != 'dialcom_przelewy') {
            return '';
        }

        if (isset($args['cc_id']) && (int)$args['cc_id'] > 0) { // recuring
            return Mage::getUrl('przelewy/przelewy/oneclick');
        }
        return Mage::getUrl('przelewy/przelewy/redirect');
    }

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getRedirectionFormData($order_id = null)
    {
        $order_id = (int) $order_id;
        if (0 >= $order_id) {
            $order_id = (int) $this->getCheckout()->getLastRealOrderId();
        }

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $storeID = Mage::helper("przelewy")->getStoreID();

        $session_id = $order_id . '|' . md5(uniqid(mt_rand(), true) . ':' . microtime(true)) . '|' . $storeID;
        $amount = number_format($order->getGrandTotal() * 100, 0, "", "");
        $currency = $order->getOrderCurrencyCode();
        $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($currency);
        $P24 = new Przelewy24Class($fullConfig['merchant_id'], $fullConfig['shop_id'], $fullConfig['salt'],
            (Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID) == '1'));

        $data = array(
            'p24_session_id' => $session_id,
            'p24_merchant_id' => (int) $fullConfig['merchant_id'],
            'p24_pos_id' => (int) $fullConfig['shop_id'],
            'p24_email' => addslashes($order->getCustomerEmail()),
            'p24_amount' => $amount,
            'p24_currency' => $currency,
            'p24_description' => 'Zamówienie ' . $order_id,
            'p24_language' => strtolower(substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2)),
            'p24_client' => addslashes($order->getBillingAddress()->getData('firstname') . ' ' . $order->getBillingAddress()->getData('lastname')),
            'p24_address' => addslashes($order->getBillingAddress()->getData('street')),
            'p24_city' => addslashes($order->getBillingAddress()->getData('city')),
            'p24_zip' => addslashes($order->getBillingAddress()->getData('postcode')),
            'p24_country' => 'PL',
            'p24_encoding' => 'utf-8',
            'p24_url_status' => Mage::getUrl('przelewy/przelewy/status'),
            'p24_url_return' => Mage::getUrl('przelewy/przelewy/success', array('ga_order_id' => $order_id)),
            'p24_api_version' => P24_VERSION,
            'p24_ecommerce' => 'magento_' . Mage::getVersion(),
            'p24_ecommerce2' => (string)Mage::getConfig()->getNode()->modules->Dialcom_Przelewy->version,
            'p24_wait_for_result' => $this->getWaitForResult() ? '1' : '0',
            'p24_shipping' => number_format($order->getShippingAmount() * 100, 0, "", ""),
        );

        $productsInfo = array();
        $lp = 0;
        foreach ($order->getAllVisibleItems() as $item) {
            $productId = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId);

            $productsInfo[] = array(
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'quantity' => (int)$item->getQtyOrdered(),
                'price' => (int)number_format($item->getPrice() * 100, 0, "", ""),
                'number' => $productId,
            );
        }

        $translations = array(
            'virtual_product_name' => Mage::helper('przelewy')->__('Extra charge [VAT and discounts]'),
            'cart_as_product' => Mage::helper('przelewy')->__('Your order'),
        );

        $shipping = (int)number_format($order->getShippingAmount() * 100, 0, "", "");
        $p24Product = new Przelewy24Product($translations);
        $p24ProductItems = $p24Product->prepareCartItems($amount, $productsInfo, $shipping);

        $data = array_merge($data, $p24ProductItems);

        $data['p24_sign'] = $P24->trnDirectSign($data);

        $info = $order->getPayment()->getMethodInstance()->getInfoInstance();
        if ((int)$info->getAdditionalInformation('method_id') > 0) {
            $data['p24_method'] = (int)$info->getAdditionalInformation('method_id');
        }

        $p24_time_limit = $this->getTimeLimit();
        if (!empty($p24_time_limit) && (int)$p24_time_limit >= 0 && (int)$p24_time_limit <= 99) {
            $data['p24_time_limit'] = (int)$p24_time_limit;
        }

        if ($this->getPaySlow()) {
            $data['p24_channel'] = 16;
        }

        if ((int)$info->getAdditionalInformation('accept_regulations') > 0) {
            $data['p24_regulation_accept'] = 1;
        }

        $P24->checkMandatoryFieldsForAction($data, 'trnDirect');

        $order->setData('p24_session_id', $session_id);
        $order->save();

        return (array)@$data;
    }

    public function getTestMode()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        return Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID);
    }

    public function getTimeLimit()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        return Mage::getStoreConfig('przelewytab1/paysettings/timelimit', $this->storeID);
    }

    public function getWaitForResult()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        return Mage::getStoreConfig('przelewytab1/paysettings/wait_for_result', $this->storeID);
    }

    public function getPaySlow()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        return Mage::getStoreConfig('przelewytab1/paysettings/payslow', $this->storeID);
    }

    public function getTotalPrice()
    {
        return number_format($this->getCheckout()->getQuote()->getBaseGrandTotal(), 2, '.', '');
    }

    public function getPaymentURI()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        $P24 = new Przelewy24Class(Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID),
            Mage::getStoreConfig('payment/dialcom_przelewy/shop_id', $this->storeID),
            Mage::getStoreConfig('payment/dialcom_przelewy/salt', $this->storeID),
            (Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID) == '1'));
        return $P24->trnDirectUrl();
    }

    public function getCountriesToOptionArray()
    {
        $new = array();
        foreach ($this->_sa_countries as $key => $option) {
            $new[] = array(
                'value' => $key,
                'label' => $option
            );
        }

        return $new;
    }

    private $_sa_countries = array(
        'AL' => 'Albania',
        'AUS' => 'Australia',
        'A' => 'Austria',
        'BY' => 'Belarus',
        'B' => 'Belgium',
        'BIH' => 'Bosnia and Herzegowina',
        'BR' => 'Brazil',
        'BG' => 'Bulgaria',
        'CDN' => 'Canada',
        'HR' => 'Croatia',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'ET' => 'Egypt',
        'EST' => 'Estonia',
        'FIN' => 'Finland',
        'F' => 'France',
        'DE' => 'Germany',
        'GR' => 'Greece',
        'H' => 'Hungary',
        'IS' => 'Iceland',
        'IND' => 'India',
        'IRL' => 'Ireland',
        'I' => 'Italy',
        'J' => 'Japan',
        'LV' => 'Latvia',
        'FL' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'L' => 'Luxembourg',
        'NL' => 'Netherlands',
        'N' => 'Norway',
        'PL' => 'Polska',
        'P' => 'Portugal',
        'RO' => 'Romania',
        'RUS' => 'Russian Federation',
        'SK' => 'Slovakia (Slovak Republic)',
        'SLO' => 'Slovenia',
        'E' => 'Spain',
        'S' => 'Sweden',
        'CH' => 'Switzerland',
        'TR' => 'Turkey',
        'UA' => 'Ukraine',
        'UK' => 'United Kingdom',
        'USA' => 'United States',
    );

    /*
     * Zwraca kwotę dodatkowej opłaty przy wyborze przelewy24 na podstawie order_id
     * @param int
     * @return float
     *
     * */
    public static function getExtrachargeAmount($order_id)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId((int)$order_id);
        $amount = number_format($order->getGrandTotal() * 100, 0, "", "");
        return self::getExtrachargeAmountByAmount($amount);
    }

    /*
     * Zwraca kwotę dodatkowej opłaty przy wyborze przelewy24 na podstawie kwoty
     * @param int
     * @return float
     *
     * */
    public static function getExtrachargeAmountByAmount($amount)
    {
        $amount = round($amount);
        $extracharge_amount = 0;
        $storeID = Mage::helper("przelewy")->getStoreID();

        if (Mage::getStoreConfig('przelewytab1/additionall/extracharge',
                $storeID) == 1 && $amount > 0 && (int)Mage::getStoreConfig('przelewytab1/additionall/extracharge_product',
                $storeID) > 0
        ) {

            $inc_amount_settings = (float)Mage::getStoreConfig('przelewytab1/additionall/extracharge_amount', $storeID);
            $inc_percent_settings = (float)Mage::getStoreConfig('przelewytab1/additionall/extracharge_percent',
                $storeID);

            $inc_amount = round($inc_amount_settings > 0 ? $inc_amount_settings * 100 : 0);
            $inc_percent = round($inc_percent_settings > 0 ? $inc_percent_settings / 100 * $amount : 0);

            $extracharge_amount = max($inc_amount, $inc_percent);

        }
        return $extracharge_amount;
    }

    /*
     * Dodaje do zamówienia produkt wirtualny, extracharge
     * @param int
     * @return void
     *
     */
    public static function addExtracharge($order_id)
    {
        $order_id = (int) $order_id;
        if (0 >= $order_id) return;
        $storeID = Mage::helper("przelewy")->getStoreID();
        $extracharge_amount = self::getExtrachargeAmount($order_id);
        $extracharge_product = (int)Mage::getStoreConfig('przelewytab1/additionall/extracharge_product', $storeID);

        if (Mage::getStoreConfig('przelewytab1/additionall/extracharge',
                $storeID) == 1 && $extracharge_amount > 0 && $extracharge_product > 0
        ) {

            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

            $product = Mage::getModel('catalog/product')->load($extracharge_product);

            $foundExtracharge = false;
            foreach ($order->getAllItems() as $item) {
                if ($item->getSku() == $product->getSku()) {
                    $foundExtracharge = true;
                }
            }

            if (!$foundExtracharge) {
                try {
                    $rowTotal = $extracharge_amount / 100;

                    $qty = 1;
                    $orderItem = Mage::getModel('sales/order_item')
                        ->setStoreId($order->getStore()->getStoreId())
                        ->setQuoteItemId(null)
                        ->setQuoteParentItemId(null)
                        ->setProductId($product->getId())
                        ->setProductType($product->getTypeId())
                        ->setQtyBackordered(null)
                        ->setTotalQtyOrdered($qty)
                        ->setQtyOrdered($qty)
                        ->setName($product->getName())
                        ->setSku($product->getSku())
                        ->setPrice($rowTotal)
                        ->setBasePrice($rowTotal)
                        ->setOriginalPrice($rowTotal)
                        ->setRowTotal($rowTotal)
                        ->setBaseRowTotal($rowTotal)
                        ->setOrder($order);
                    $orderItem->save();

                    $quote = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter("entity_id",
                        $order->getQuoteId())->getFirstItem();

                    $grandTotal = $order->getGrandTotal();
                    $baseGrandTotal = $order->getBaseGrandTotal();

                    $order->setGrandTotal($grandTotal + $rowTotal);
                    $order->setBaseGrandTotal($baseGrandTotal + $rowTotal);

                    $quote->save();
                    $order->save();
                } catch (Exception $e) {
                    Mage::log(__METHOD__ . ' ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * @param $order
     * @return bool
     */
    public static function confirmZenCardDiscount($order)
    {
        $result = false;
        $storeID = Mage::helper("przelewy")->getStoreID();

        $merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $storeID);
        $apiKey = Mage::getStoreConfig('przelewytab1/klucze/api_key', $storeID);
        $zenCardApi = new ZenCardApi($merchantId, $apiKey);

        try {
            $quote = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter("entity_id",
                $order->getQuoteId())->getFirstItem();
            $amount = $quote->getSubtotal() * 100;
            $storeUrl = Mage::getBaseUrl();
            $orderId = $quote->getId();
            $zenCardOrderId = $zenCardApi->buildZenCardOrderId($orderId, $storeUrl);
            $confirm = $zenCardApi->confirm($zenCardOrderId, $amount);
            Mage::log($confirm, null, 'zencard-confirm.log');

            if ($confirm->isVerified() && $confirm->isConfirmed() && $confirm->withZencard()) {
                $order->addStatusHistoryComment($confirm->getInfo());
                $order->save();
                $result = $confirm->isConfirmed();
            }
        } catch (\Exception $e) {
            Mage::log(__METHOD__ . ' ' . $e->getMessage());
        }

        return $result;
    }

    public function getPaymentMethodCode()
    {
        return $this->_code;
    }
}