<?php

class Dialcom_Przelewy_Block_Adminhtml_Order_View_Tab_Refunds extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    private $merchantId;
    private $apiKey;
    private $crc;
    private $sandbox = false;
    private $order;

    /** @var int */
    private $storeID;

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('dialcom/przelewy/order/view/tab/refunds.phtml');
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        $this->merchantId = Mage::getStoreConfig('payment/dialcom_przelewy/merchant_id', $this->storeID);
        $this->crc = Mage::getStoreConfig('payment/dialcom_przelewy/salt', $this->storeID);
        $this->apiKey = Mage::getStoreConfig('przelewytab1/klucze/api_key', $this->storeID);
        $this->sandbox = Mage::getStoreConfig('payment/dialcom_przelewy/mode', $this->storeID) == '1';
    }

    public function getTabLabel()
    {
        return $this->__('Przelewy24 - Refunds');
    }

    public function getTabTitle()
    {
        return $this->__('Przelewy24 - Refunds');
    }

    public function canShowTab()
    {
        return $this->getPaymentMethod() === 'dialcom_przelewy';
    }

    public function isHidden()
    {
        return false;
    }

    public function getOrder()
    {
        $orderFromRegistry = Mage::registry('current_order');
        if (!is_null($orderFromRegistry)) {
            $this->order = $orderFromRegistry;
            return $this->order;
        }

        return $this->order;
    }

    /**
     * @param $orderId
     */
    public function setOrder($orderId)
    {
        $this->order = Mage::getModel('sales/order')->load($orderId);
    }

    private function getPaymentMethod()
    {
        return $this->getOrder()->getPayment()->getMethodInstance()->getCode();
    }

    public function isSoapExtensionInstalled()
    {
        return extension_loaded('soap');
    }

    /**
     * @return array
     */
    public function getRefunds()
    {
        $result = array(
            'amount' => $this->getOrder()->getGrandTotal(),
            'refunds' => array()
        );

        $refunds = array();

        try {
            $url = $this->getWSUrl();
            $sessionId = substr($this->order->getData('p24_session_id'),0,100);
            $p24OrderId = $this->getOrderIdBySessionId($sessionId);
            $soap = new \SoapClient($url, array('cache_wsdl' => WSDL_CACHE_NONE));
            $wsResult = $soap->GetRefundInfo($this->merchantId, $this->apiKey, $p24OrderId);

            if (!empty($wsResult->result)) {
                $refunds['maxToRefund'] = 0;
                foreach ($wsResult->result as $key => $value) {
                    $refunds['refunds'][$key]['amount_refunded'] = $value->amount;
                    $date = new \DateTime($value->date);
                    $refunds['refunds'][$key]['created'] = $date->format('Y-m-d H:i:s');
                    $refunds['refunds'][$key]['status'] = $this->getStatusMessage($value->status);

                    if ($value->status === 1 || $value->status === 3) {
                        $refunds['maxToRefund'] += $value->amount;
                    }
                }
            }

            if (!empty($refunds)) {
                $result['amount'] -= ($refunds['maxToRefund'] / 100);
                $result['refunds'] = $refunds['refunds'];
            }
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getWSUrl()
    {
        $mode = $this->sandbox ? 'sandbox' : 'secure';
        $url = 'https://' . $mode . '.przelewy24.pl/external/' . $this->merchantId . '.wsdl';

        return $url;
    }

    /**
     * @param $status
     * @return string
     */
    private function getStatusMessage($status)
    {
        switch ($status) {
            case 0:
                $statusMessage = Mage::helper('przelewy')->__('Error');
                break;
            case 1:
                $statusMessage = Mage::helper('przelewy')->__('Completed');
                break;
            case 2:
                $statusMessage = Mage::helper('przelewy')->__('Suspended');
                break;
            case 3:
                $statusMessage = Mage::helper('przelewy')->__('Pending');
                break;
            case 4:
                $statusMessage = Mage::helper('przelewy')->__('Rejected');
                break;
            default:
                $statusMessage = Mage::helper('przelewy')->__('Unknown status');
        }

        return $statusMessage;
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('adminhtml/przelewy/refunds', array('order_id' => $this->getOrder()->getId()));
    }


    /**
     * @param $orderId
     * @param $amountToRefund
     * @return string
     */
    public function refundProcess($orderId, $amountToRefund)
    {
        $this->order = Mage::getModel('sales/order')->load((int)$orderId);
        $sessionId = addslashes($this->order->getData('p24_session_id'));

        $refunds = array(
            0 => array(
                'sessionId' => $sessionId,
                'orderId' => $this->getOrderIdBySessionId($sessionId),
                'amount' => $amountToRefund * 100
            )
        );

        $response = $this->refundTransaction($refunds);
        $result = $this->prepareRefundResponse($response);

        return $result;
    }

    /**
     * @param $refunds
     * @return string
     */
    private function refundTransaction($refunds)
    {
        try {
            $url = $this->getWSurl();
            $apiKey = preg_match('/^[0-9a-z]{32}/', $this->apiKey) ? $this->apiKey : '0';
            $soap = new \SoapClient($url);
            $response = $soap->refundTransaction(
                (int) $this->merchantId,
                $apiKey,
                time(),
                (array) $refunds
            );

            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $refundResponse
     * @return array
     */
    private function prepareRefundResponse($refundResponse)
    {
        try {
            $result = array(
                'error' => true,
                'success' => false,
                'message' => Mage::helper('przelewy')->__('Refund processing error!'),
            );

            if (isset($refundResponse->result)) {
                foreach ($refundResponse->result as $key => $value) {
                    if ((int)$value->status === 1) {
                        $result['error'] = false;
                        $result['success'] = true;
                        $result['message'] = Mage::helper('przelewy')->__('Refund was successful!');
                    } else {
                        if (isset($value->error)) {
                            $result['message'] = $value->error;
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            $result = array(
                'message' => Mage::helper('przelewy')->__('Refund processing error!')
            );
        }

        return $result;
    }

    /**
     * @param $allowedAmount
     * @return string
     */
    public function buildRefundsForm($allowedAmount)
    {
        if ($allowedAmount > 0) {
            $form = '<label for="amountToRefund">' . Mage::helper('przelewy')->__('Amount') . '</label>
                  <input id="amountToRefund" type="number" name="amountToRefund"
                         value="' . $allowedAmount . '" min="0.01" max="' . $allowedAmount . '" step="0.01"/>
                  <input type="hidden" id="maxAmount" name="maxAmount" value="' . $allowedAmount . '"/>';
        } else {
            $form = '<span class="field-row"><ul class="messages"><li class="warning-msg">' .
                Mage::helper('przelewy')->__('The payment has already been fully refunded - no funds to make further returns.') .
                '</li></ul></span>';
        }

        return $form;
    }

    /**
     * @param $refunds
     * @return string
     */
    public function buildRefundsTable($refunds)
    {
        if (empty($refunds)) {
            return '<span class="field-row" id="refundsListErrorMessage"><ul class="messages"><li class="warning-msg">' .
            Mage::helper('przelewy')->__('There is no refunds.') .
            '</li></ul></span>';
        }

        $dateLabel = Mage::helper('przelewy')->__('Date of refund');
        $amountLabel = Mage::helper('przelewy')->__('Amount refunded');
        $table = <<< HTML
                        <colgroup>
                            <col width="10%">
                            <col width="45%">
                            <col width="35%">
                            <col width="10%">
                        </colgroup>
                        <thead>
                        <tr class="headings">
                            <th class="a-center">
                                <span class="nobr">L.p.</span></th>
                            <th class="a-center">
                                <span class="nobr">{$dateLabel}</span>
                            </th>
                            <th class="a-center">
                                <span class="nobr">{$amountLabel}</span>
                            </th>
                            <th class="a-center">
                                <span class="nobr">Status</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
HTML;

        foreach ($refunds as $key => $refund) {
            $lp = $key + 1;
            $amount = Mage::helper('core')->currency((int)(($refund['amount_refunded']) / 100), true, false);
            $refundCreated = filter_var($refund['created'],FILTER_SANITIZE_STRING);
            $refundStatus = filter_var($refund['status'],FILTER_SANITIZE_STRING);
            $table .= <<< HTML
                            <tr class="border">
                                <td class="a-center">{$lp}</td>
                                <td class="a-center">{$refundCreated}</td>
                                <td class="a-center"><strong>{$amount}</strong></td>
                                <td class="a-center">{$refundStatus}</td>
                            </tr>
HTML;
        }
        $table .= <<< HTML
                        </tbody>
HTML;

        return $table;
    }

    /**
     * @param $sessionId
     * @return int
     */
    private function getOrderIdBySessionId($sessionId)
    {
        $orderId = 0;
        try {
            $apiKey = preg_match('/^[0-9a-z]{32}/', $this->apiKey) ? $this->apiKey : '0';
            $url = $this->getWSurl();
            $soap = new \SoapClient($url);
            $response = $soap->GetTransactionBySessionId(
                (int)$this->merchantId,
                $apiKey,
                substr($sessionId, 0, 100)
            );

            if (isset($response->result) && isset($response->result->orderId)) {
                $orderId = $response->result->orderId;
            }

        } catch (\Exception $e) {
        }

        return $orderId;
    }
}