<?php
require_once Mage::getBaseDir('code') . '/' . Mage::getConfig()->getNode('modules/Dialcom_Przelewy/codePool') . '/Dialcom/Przelewy/class_przelewy24.php';

class Dialcom_Przelewy_PrzelewyController extends Mage_Core_Controller_Front_Action
{

    /** @var int */
    private $storeID;

    public function thanksAction()
    {
        $session = Mage::getSingleton('core/session');
        $session->addSuccess($this->__('Thank you for your payment.') . '<script>window.setTimeout(function(){ window.location="' . Mage::getUrl('/') . '"},3000)</script>');
        $this->loadLayout();
        $this->renderLayout();
    }

    public function summaryAction()
    {
        $key = $this->getRequest()->getParam('key');

        $order_id = (int)$this->getRequest()->getParam('order_id');
        $_order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $currency = $_order->getOrderCurrencyCode();
        $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($currency);
        $right_key = md5($fullConfig['merchant_id'] . '|' . $order_id);

        if ($_order->getBaseTotalDue() == 0 || $key != $right_key) {
            return $this->_redirect('/');
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setPrzelewyQuoteId($session->getQuoteId());

        $order_id = (int)Mage::getSingleton('checkout/session')->getLastRealOrderId();
        if ($order_id) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            $paymentData = $order->getPayment()->getData();
            $additionalInformation = $paymentData['additional_information'];

            Dialcom_Przelewy_Model_Payment_Przelewy::addExtracharge($order_id);

            $this->storeID = Mage::helper("przelewy")->getStoreID();

            $payinshop = (int)Mage::getStoreConfig('przelewytab1/oneclick/payinshop', $this->storeID);
            $gaBeforePayment = (int)Mage::getStoreConfig('przelewytab1/additionall/ga_before_payment', $this->storeID);

            if ($payinshop && in_array($additionalInformation['method_id'],
                    Dialcom_Przelewy_Model_Recuring::getChannelsCards())
            ) {
                $this->loadLayout();
                Mage::register('payInShopByCard', true);
                $this->renderLayout();
            } else {
                if ($gaBeforePayment === 1) {
                    $this->loadLayout();
                    $this->renderLayout();
                } else {
                    $this->getResponse()->setBody(
                        $this->getLayout()->createBlock('przelewy/payment_przelewy_redirect')->getHtml()
                    );
                }
            }
            $session->unsQuoteId();
        }
    }

    private function makeInvoiceFromOrder($order)
    {
        try {
            if ($order->canInvoice()) {
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                if ($invoice->getTotalQty()) {
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                    $invoice->register();
                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $transactionSave->save();
                }
            }
        } catch (Exception $e) {
            error_log(__METHOD__ . ' ' . $e->getMessage());
        }
    }

    public function oneclickAction()
    {
        $zenCardConfirmed = true;
        $order_id = (int) Mage::getSingleton('checkout/session')->getLastRealOrderId();
        Dialcom_Przelewy_Model_Payment_Przelewy::addExtracharge($order_id);

        $this->storeID = Mage::helper("przelewy")->getStoreID();

        if ((int)Mage::getStoreConfig('przelewytab1/additionall/zencard', $this->storeID) === 1) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            $discount = $order->getData('discount_total');
            if (!is_null($discount)) {
                $zenCardConfirmed = Dialcom_Przelewy_Model_Payment_Przelewy::confirmZenCardDiscount($order);
                if (!$zenCardConfirmed) {
                    $order->addStatusHistoryComment($this->__('The payment with ZenCard was not confirmed.'));
                    $order->save();
                }
            }
        }

        if ($zenCardConfirmed) {
            $paymentData = Mage::getModel('sales/order')->loadByIncrementId($order_id)->getPayment()->getData();
            $additionalInformation = $paymentData['additional_information'];
            $recuring = Mage::getModel('recuring/recuring');
            $result = $recuring->chargeCard($order_id, (int)$additionalInformation['cc_id']);
            $this->storeID = Mage::helper("przelewy")->getStoreID();

            if (!!$result) {
                $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

                $chgState = (int)Mage::getStoreConfig('przelewytab1/paysettings/chg_state', $this->storeID);
                $mkInvoice = (int)Mage::getStoreConfig('przelewytab1/paysettings/mk_invoice', $this->storeID);

                if ($chgState == 1) {
                    $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING,
                        Mage::helper('przelewy')->__('The payment has been accepted.'), true);
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                    $order->save();
                    if ($mkInvoice == 1) {
                        $this->makeInvoiceFromOrder($order);
                        $order->sendOrderUpdateEmail();
                    }
                }

                $order->save();
                $this->successAction();
            } else {
                $this->failureAction();
            }
        } else {
            $this->failureAction();
        }
    }

    private function verifyTransaction($order_id, $storeID = 0)
    {
        $storeID = (int) $storeID;
        $order_id = (int)$order_id;
        if (!($order_id > 0 && $storeID >= 0)) return false;

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

        $payment = $order->getPayment();
        if ($payment) {
            $payment->setData('transaction_id', (int) $_POST['p24_order_id']);
            $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
        }
        $currency = $order->getOrderCurrencyCode();

        if(!$storeID) {
            $storeID = Mage::helper("przelewy")->getStoreID();
        }

        $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($currency, $storeID);
        $P24 = new Przelewy24Class($fullConfig['merchant_id'], $fullConfig['shop_id'], $fullConfig['salt'],
            (Mage::getStoreConfig('payment/dialcom_przelewy/mode', $storeID) == '1'));

        $ret = $P24->trnVerifyEx(array(
            'p24_amount' => number_format($order->getGrandTotal() * 100, 0, "", ""),
            'p24_currency' => $currency
        ));

        if ($ret !== null) {
            $sendOrderUpdateEmail = false;
            if ($ret === true) {
                $chgState = (int)Mage::getStoreConfig('przelewytab1/paysettings/chg_state', $storeID);
                $mkInvoice = (int)Mage::getStoreConfig('przelewytab1/paysettings/mk_invoice', $storeID);

                if ($chgState == 1) {
                    if ($order->getState() != Mage_Sales_Model_Order::STATE_PROCESSING) {
                        $sendOrderUpdateEmail = true;
                    }
                    $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING,
                        Mage::helper('przelewy')->__('The payment has been accepted.'), true);
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                    $order->save();
                    if ($mkInvoice == 1) {
                        $this->makeInvoiceFromOrder($order);
                    }
                }
                $order->save();

                // zapis karty
                if (in_array((int)$_POST['p24_method'], Dialcom_Przelewy_Model_Recuring::getChannelsCards())) {
                    $recuring = Mage::getModel('recuring/recuring');
                    $recuring->saveUsedCard($order->getData('customer_id'), (int)$_POST['p24_order_id']);
                }
            } else {
                if ($order->getState() != Mage_Sales_Model_Order::STATE_HOLDED) {
                    $sendOrderUpdateEmail = true;
                }

                $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_HOLDED,
                    Mage::helper('przelewy')->__('Payment error.') . ' ' . addslashes($ret['errorMessage']), true);
                $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true);
            }
            if ($sendOrderUpdateEmail == true) {
                $order->sendOrderUpdateEmail();
            }
            $order->save();

            return $ret === true;
        }

        return false;
    }

    public function statusAction()
    {
        $result = false;
        if (isset($_POST['p24_session_id'])) {
            $sa_sid = explode('|', $_POST['p24_session_id']);
            $order_id = (int)$sa_sid[0];
            $storeID = (int)$sa_sid[2];

            $result = $this->verifyTransaction($order_id, $storeID);
        }

        echo $result ? 'OK' : 'ERROR';
        exit;
    }

    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $order_id = (int) $session->getLastRealOrderId();
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        if (is_null($order_id)) {
            return $this->thanksAction();
        } else {
            $chgState = (int)Mage::getStoreConfig('przelewytab1/paysettings/chg_state', $this->storeID);
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

            if ($chgState == 1 && $order->getState() != Mage_Sales_Model_Order::STATE_PROCESSING) {
                $this->failureAction();
            }

            $ga_order_id = (int) $this->getGaOrderId($order_id);
            $session->getQuote()->setIsActive(false)->save();
            $this->_redirect('checkout/onepage/success', array('ga_order_id' => $ga_order_id));
        }
    }

    public function failureAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPrzelewyQuoteId(true));
        $session->addError($this->__("Your payment was not confirmed by Przelewy24. Contact with your seller for more information."));
        $this->_redirect('checkout/cart');
    }

    public function mycardsAction()
    {
        $oneclick = Mage::getUrl('przelewy/przelewy/oneclick');
        if (!$oneclick) {
            return $this->_redirect('customer/account');
        }


        $cardRm = (int)Mage::app()->getRequest()->getParam('cardrm');
        if ($cardRm > 0) {
            Dialcom_Przelewy_Model_Recuring::unregisterCard($cardRm);
        }

        $cardForgetAction = Mage::app()->getRequest()->getParam('cardforget');
        if ($cardForgetAction > 0) {
            $p24_forget = (bool)Mage::app()->getRequest()->getParam('p24_forget');
            Dialcom_Przelewy_Model_Recuring::setP24Forget($p24_forget);
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function paymentAction()
    {
        $order_id = (int) Mage::getSingleton('checkout/session')->getLastRealOrderId();

        if ($order_id) {
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('przelewy/payment_przelewy_redirect')->getHtml()
            );
        }
    }

    public function paymentlinkAction()
    {
        $order_id = (int) $this->getRequest()->getParam('order_id');
        $session = Mage::getSingleton('checkout/session');
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        if ($order_id) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            $currency = $order->getOrderCurrencyCode();
            $fullConfig = Dialcom_Przelewy_Model_Config_Waluty::getFullConfig($currency);
            try {
                $templateId = (int)Mage::getStoreConfig('przelewytab1/paysettings/sendlink_mailtemplate',
                    $this->storeID);
                if ($templateId > 0) {
                    $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
                } else {
                    $emailTemplate = Mage::getModel('core/email_template')->loadDefault('p24_default_mail_with_link');
                }

                $right_key = md5($fullConfig['merchant_id'] . '|' . $order->getIncrementId());
                $order->payment_link = Mage::getUrl('przelewy/przelewy/summary',
                    array('order_id' => $order->getIncrementId(), 'key' => $right_key));
                $vars = array('order' => $order);

                $receiveEmail = filter_var($order->getCustomerEmail(), FILTER_SANITIZE_EMAIL);
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
            } catch (Exception $e) {
                $session->addError($this->__('Failed to send the payment email.'));
                Mage::logException($e);
            }

            $this->_redirect('/');
        }
        return;
    }

    private function getGaOrderId($order_id)
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        $payinshop = (int)Mage::getStoreConfig('przelewytab1/oneclick/payinshop', $this->storeID);
        $gaBeforePayment = (int)Mage::getStoreConfig('przelewytab1/additionall/ga_before_payment', $this->storeID);
        $order = Mage::getModel('sales/order')->loadByIncrementId((int) $order_id);
        $paymentData = $order->getPayment()->getData();
        $additionalInformation = $paymentData['additional_information'];
        $payInShopByCard = $payinshop && in_array($additionalInformation['method_id'],
                Dialcom_Przelewy_Model_Recuring::getChannelsCards());

        $ga_order_id = ($gaBeforePayment === 1 && !$payInShopByCard) ? 0 : Mage::app()->getRequest()->getParam('ga_order_id',
            0);

        return (int) $ga_order_id;
    }
}
