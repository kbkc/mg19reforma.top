<?php

class Dialcom_Przelewy_Adminhtml_PrzelewyController extends Mage_Adminhtml_Controller_Action
{

    /** @var int */
    private $storeID;

    public function paymentivrAction()
    {
        $order_id = (int) $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);

        Dialcom_Przelewy_Model_Config_Kanaly::runIvrPayment($order);

        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
    }

    public function paymentemailAction()
    {
        $order_id = (int) $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);
        $currency = $order->getOrderCurrencyCode();
        $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($currency);
        $session = Mage::getSingleton('adminhtml/session')->init('core', 'adminhtml');
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        try {
            $templateId = (int)Mage::getStoreConfig('przelewytab1/paysettings/sendlink_mailtemplate', $this->storeID);
            if ($templateId > 0) {
                $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
            } else {
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault('p24_default_mail_with_link');
            }
            $emailTemplate->setTemplateSubject($this->__('Payment for order ').$order->getIncrementId());
            $right_key = md5($fullConfig['merchant_id'] . '|' . $order->getIncrementId());
            $order->payment_link = Mage::getUrl('przelewy/przelewy/summary',
                array('order_id' => $order->getIncrementId(), 'key' => $right_key));

            $vars = array(
                'order' => $order
            );

            $receiveEmail = $order->getCustomerEmail();
            $receiveName = $order->getBillingAddress()->getData('firstname') . ' ' . $order->getBillingAddress()->getData('lastname');

            $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $this->storeID));
            $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $this->storeID));

            $successFlag = $emailTemplate->send($receiveEmail, $receiveName, $vars);

            $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('przelewy')->__('The link for payment with Przelewy24 has been sent by e-mail'));
            $order->save();
            if ($successFlag) {
                $session->addSuccess($this->__('The link for payment with Przelewy24 has been sent by e-mail'));
            } else {
                $session->addError($this->__('Failed to send the payment email.'));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
            Mage::logException($e);
        } catch (Exception $e) {
            $session->addError($this->__('Failed to send the payment email.'));
            Mage::logException($e);
        }
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
        return;
    }

    /**
     * @return Mage_Core_Controller_Response_Http
     */
    public function refundsAction()
    {
        $this->getResponse()->clearHeaders()->setHeader(
            'Content-type',
            'application/json'
        );

        $params = $this->getRequest()->getParams();
        $amountToRefund = floatval($params['amountToRefund']);
        $maxAmount = floatval($params['maxAmount']);
        $minAmount = 0.01;

        if ($amountToRefund > $maxAmount || $amountToRefund < $minAmount || !is_numeric($amountToRefund)) {
            $response = array(
                'error' => true,
                'message' => $this->__('Allowed amount range') . ': ' . $minAmount . ' - ' . $maxAmount
            );
        } else {
            $response = $this->refundsService($params);
        }

        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode($response)
        );

        return $this->getResponse();
    }

    /**
     * @param $params
     * @return array
     */
    private function refundsService($params)
    {
        try {
            $orderId = (int) $params['order_id'];
            $amountToRefund = (int) $params['amountToRefund'];
            $maxAmount = (int) $params['maxAmount'];
            $allowedAmount = $maxAmount - $amountToRefund;
            $block = Mage::app()->getLayout()->createBlock('przelewy/adminhtml_order_view_tab_refunds');

            $refundProceed = $this->refundProceed($orderId, $amountToRefund);
            if (!$refundProceed['error'] && $refundProceed['success']) {
                $block->setOrder($orderId);
                $refunds = $block->getRefunds();
                $allowedAmount = isset($refunds['amount']) ? $refunds['amount'] : ($maxAmount - $amountToRefund);
                $refundsToTable = isset($refunds['refunds']) && is_array($refunds['refunds']) ? $refunds['refunds'] : array();
                return array(
                    'error' => false,
                    'message' => $refundProceed['message'],
                    'data' => array(
                        'allowedAmount' => Mage::helper('core')->currency($allowedAmount, true, false),
                        'blocked' => $allowedAmount <= 0,
                        'form' => $block->buildRefundsForm($allowedAmount),
                        'table' => $block->buildRefundsTable($refundsToTable)
                    )
                );
            }

            return array(
                'error' => true,
                'message' => $refundProceed['message'],
                'data' => array(
                    'allowedAmount' => Mage::helper('core')->currency($allowedAmount, true, false),
                    'blocked' => $allowedAmount <= 0,
                    'form' => $block->buildRefundsForm($allowedAmount),
                    'table' => $block->buildRefundsTable(array())
                )
            );
        } catch (\Exception $ex) {
            return array(
                'error' => true,
                'message' => $ex->getMessage(),
                'data' => array()
            );
        }
    }


    /**
     * @param $orderId
     * @param $amountToRefund
     * @return mixed
     */
    private function refundProceed($orderId, $amountToRefund)
    {
        $block = Mage::app()->getLayout()->createBlock('przelewy/adminhtml_order_view_tab_refunds');
        $refundResponse = $block->refundProcess($orderId, $amountToRefund);

        return $refundResponse;
    }

}