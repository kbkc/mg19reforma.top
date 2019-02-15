<?php

class Itdelight_QuickOrder_IndexController extends Mage_Core_Controller_Front_Action
{
    protected function errRedirect($mess)
    {
        Mage::getSingleton('core/session')->addError($mess);
        return $this->_redirect("/");
    }

    public function warehouse_cityAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $ref = array_keys(Mage::app()->getRequest()->getParams())[0];
            if ($ref == "") {
                echo json_encode(
                    array(
                        array(
                            'Ref' => '',
                            'DescriptionRu' => $this->__('Choose the warehouse')
                        )
                    ));
                return;
            }
            $result = Mage::helper('sy_novaposhta')->findWarehouses($ref, true);
            echo json_encode($result);
            return;
        } else {
            $this->norouteAction();
            return;
        }
    }

    public function place_orderAction()
    {
        $params = Mage::app()->getRequest()->getParams();
        if($params['price-data']){
            foreach(json_decode($params['price-data']) as $rule){
                Mage::getModel('checkout/cart')->getQuote()->getItemByProduct(Mage::getModel('catalog/product')
                    ->load($rule[0]))->setQty($rule[1])->save();
            }
        }

        $productids = array();
        foreach (Mage::getModel('checkout/cart')->getQuote()->getAllItems() as $item) {
            $productids[] = $item->getId();
        }
        if (!count($productids)) {
            $this->errRedirect('Cart is empty');
            return;
        }
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        // Start New Sales Order Quote
        $quote = Mage::getModel('checkout/cart')->getQuote()->setStoreId($store->getId());


        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId($websiteId)
            ->loadByEmail($params['email']);
        if ($customer->getId()) {
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($params['first_name'])
                ->setLastname($params['last_name'])
                ->setEmail($params['email']);
        } else {
            if($params['email']) {

                $pass = substr(md5(uniqid()), 0, 10);

                $html = '<p>' . $params['email'] . '</p></br><p>' . $pass . '</p></br><p><a href="' . Mage::getBaseUrl() . '">' . Mage::getBaseUrl() . '</a></p>';

                $mail = Mage::getModel('core/email');
                $mail->setToName($params['first_name']);
                $mail->setToEmail($params['email']);
                $mail->setBody($html);
                $mail->setSubject('Registration');
                $mail->setFromEmail('');
                $mail->setFromName("");
                $mail->setType('html');

                try {
                    $mail->send();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError('Unable to send email.');
                }

                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($params['first_name'])
                    ->setLastname($params['last_name'])
                    ->setEmail($params['email'])
                    ->setPassword($pass)
                    ->save();
            } else{
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($params['first_name'])
                    ->setLastname($params['last_name'])
                    ->setEmail('empty@mail.com');
            }
        }

        // Assign Customer To Sales Order Quote
        $quote->assignCustomer($customer);

        // Set Sales Order Billing Address
        $city = trim($params['city']);

        $street = $params['warehouse'];
        $shipping = 'sy_novaposhta_type_WarehouseWarehouse';
        if ($params['destination'] == "address") {
            $shipping = 'sy_novaposhta_type_WarehouseDoors';
            $street = $params['street'] . ' ' . $params['house'] . ' ' . $params['apartment'] . ' ';
        }
        $quote->getBillingAddress()->addData(array(
            'customer_address_id' => '',
            'prefix' => '',
            'firstname' => $params['first_name'],
            'lastname' => $params['last_name'],
            'suffix' => '',
            'company' => '',
            'street' => array(
                '0' => $street,
                '1' => ''
            ),
            'city' => $city,
            'country_id' => 'UA',
            'region' => 'UA',
            'postcode' => '0000',
            'telephone' => $params['tel'],
            'fax' => '',
            'vat_id' => '',
        ));

        // Set Sales Order Shipping Address
        $shippingAddress = $quote->getShippingAddress()->addData(array(
            'customer_address_id' => '',
            'prefix' => '',
            'firstname' => $params['first_name'],
            'lastname' => $params['last_name'],
            'suffix' => '',
            'company' => '',
            'street' => array(
                '0' => $street,
                '1' => ''
            ),
            'city' => $city,
            'country_id' => 'UA',
            'region' => 'UA',
            'postcode' => '0000',
            'telephone' => $params['tel'],
            'fax' => '',
            'vat_id' => '',
        ));

        // Collect Rates and Set Shipping & Payment Method
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod($shipping)
            ->setPaymentMethod($params['payment']);

        // Set Sales Order Payment
        $quote->getPayment()->importData(array('method' => $params['payment']));

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        try {
            // Create Order From Quote
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            $increment_id = $service->getOrder()->getRealOrderId();
            $order = $service->getOrder();
            if($params['comment']){
                $order->addStatusHistoryComment($params['comment']);
            }
            $order->save();
            if ($order->getCanSendNewEmailFlag()){
                $order->sendNewOrderEmail();
            }
            Mage::getSingleton('checkout/session')->setLastRealOrderId($increment_id);
            Mage::getSingleton('checkout/session')->setQuoteId($quote->getId());
            $this->_redirect('popuporder/index/success');
        } catch (Exception $ex) {
            $ex->getMessage();
            return $this->_redirect("*/*");
        } catch (Mage_Core_Exception $e) {
            $e->getMessage();
            return $this->_redirect("*/*");
        }
    }

    public function delete_itemAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                Mage::getSingleton('checkout/cart')->removeItem($id)->save();
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }

    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        if ($orderId = $session->getLastRealOrderId()) {
            $coreSession = Mage::getSingleton('core/session');
            $coreSession->setLastRealOrderId($orderId);
            $coreSession->setQuoteId($session->getQuoteId());
            $session->clear();
            $session->setQuoteId(null);
            $session->setLastRealOrderId(null);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('/');
        }

    }
}