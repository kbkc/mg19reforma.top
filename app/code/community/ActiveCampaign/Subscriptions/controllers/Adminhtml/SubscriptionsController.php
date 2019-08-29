<?php

define("ACTIVECAMPAIGN_URL", "");
define("ACTIVECAMPAIGN_API_KEY", "");
require_once(Mage::getBaseDir() . "/app/code/community/ActiveCampaign/Subscriptions/activecampaign-api-php/ActiveCampaign.class.php");

class ActiveCampaign_Subscriptions_Adminhtml_SubscriptionsController extends Mage_Adminhtml_Controller_action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('subscriptions/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Connections Manager'), Mage::helper('adminhtml')->__('Connection Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('subscriptions/subscriptions')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('subscriptions_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('subscriptions/items');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit'))
                ->_addLeft($this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('subscriptions')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {

        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('subscriptions/subscriptions');

            $api_url = $data["api_url"];
            $api_key = $data["api_key"];

            $ac = new ActiveCampaign($api_url, $api_key);

            $test_connection = $ac->credentials_test();

            if (!$test_connection) {
                Mage::getSingleton("adminhtml/session")->addError("Invalid API URL or Key. Please check to make sure both values are correct.");
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } else {
                // get AC account details
                $account = $ac->api("account/view");
                $data["account_url"] = $account->account;

                $list_values = $data["list_value"];
                $list_ids = array();

                // example (converts to): ["mthommes6.activehosted.com-5","mthommes6.activehosted.com-13"]
                $data["list_value"] = json_encode($data["list_value"]);
                $data["form_value"] = json_encode($data["form_value"]);

                $model->setData($data)->setId($this->getRequest()->getParam('id'));

                if (isset($data["export_confirm"]) && (int)$data["export_confirm"]) {
                    // exporting Newsletter contacts to ActiveCampaign

                    // Try a couple different ways to get the contact data.
                    // Some versions of Magento may only support one way or another.
                    $contacts_magento = Mage::getResourceModel('newsletter/contact_collection');
                    if ($contacts_magento) {
                        $contacts_magento = $contacts_magento->showStoreInfo()->showCustomerInfo()->getData();
                    } else {
                        $contacts_magento = Mage::getModel('newsletter/subscriber')->getCollection()->getData();
                    }

                    $contacts_ac = array();

                    foreach ($list_values as $acct_listid) {
                        // IE: mthommes6.activehosted.com-13
                        $acct_listid = explode("-", $acct_listid);
                        $list_ids[] = (int)$acct_listid[1];
                    }

                    foreach ($contacts_magento as $contact) {
                        $contacts_ac_ = array(
                            "email" => isset($contact["contact_email"]) ? $contact["contact_email"] : $contact["subscriber_email"],
                            "first_name" => $contact["customer_firstname"],
                            "last_name" => $contact["customer_lastname"],
                        );

                        // add lists
                        $p = array();
                        $status = array();
                        foreach ($list_ids as $list_id) {
                            $p[$list_id] = $list_id;
                            $status[$list_id] = 1;
                        }

                        $contacts_ac_["p"] = $p;
                        $contacts_ac_["status"] = $status;

                        $contacts_ac[] = $contacts_ac_;
                    }

                    $contacts_ac_serialized = serialize($contacts_ac);

                    try {
                        $ac->api("contact/sync?service=magento", $contacts_ac_serialized);
                    } catch (Exception $e) {
                        // catch this because even a successful call throws an exception
                    }
                }

                try {
                    if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                        $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                    } else {
                        $model->setUpdateTime(now());
                    }

                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('subscriptions')->__('Settings were successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }

                    $this->_redirect('*/*/');
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('subscriptions')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('subscriptions/subscriptions');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $subscriptionsIds = $this->getRequest()->getParam('subscriptions');
        if (!is_array($subscriptionsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($subscriptionsIds as $subscriptionsId) {
                    $subscriptions = Mage::getModel('subscriptions/subscriptions')->load($subscriptionsId);
                    $subscriptions->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($subscriptionsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $subscriptionsIds = $this->getRequest()->getParam('subscriptions');
        if (!is_array($subscriptionsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($subscriptionsIds as $subscriptionsId) {
                    $subscriptions = Mage::getSingleton('subscriptions/subscriptions')
                        ->load($subscriptionsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($subscriptionsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'subscriptions.csv';
        $content = $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'subscriptions.xml';
        $content = $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        return true;
    }
}
