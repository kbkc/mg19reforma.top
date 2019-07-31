<?php

class Smartbees_Gift_Model_Observer
{
    const ADD_GIFT_ACTION = 'add_gift';

    /**
     * @param $giftSku
     * @return array
     */
    protected function getSkuList($giftSku)
    {
        return array_map('trim', explode(',', $giftSku));
    }

    /**
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Model_Store_Exception
     */
    public function salesQuoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $originalStore = $quote->getStoreId();
        $quote->setStoreId(Mage::app()->getStore()->getId());

        foreach ($quote->getAllItems() as $item) {
            if ($item->getIsGift() && $item->getGiftCustomPrice() == 0) {
                $quote->removeItem($item->getId());
            }
//            if ($item->getGiftCustomPrice() !== 0) {
//                $item->setOrginalCustomPrice($item->getGiftCustomPrice());
//                $quote->save();
//            }
        }

        $quote->setStoreId($originalStore);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function salesruleValidatorProcess(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $item = $observer->getEvent()->getItem();
        $rule = $observer->getEvent()->getRule();

        if ($rule->getSimpleAction() != self::ADD_GIFT_ACTION
            || $item->getIsGift()
            || $rule->getIsApplied())
        {
            return;
        }

        try {
            $qty = (int)$rule->getDiscountAmount();
            $skus = $this->getSkuList($rule->getGiftSku());
            foreach ($skus as $sku) {

                $freeItem = $this->getFreeQuoteItem($rule->getId(), $sku, $quote->getStoreId(), $qty);
                $quote->addItem($freeItem);
                $this->setQuoteItemTaxPercent($freeItem);
                $freeItem->setApplyingRule($rule);

            }
            $rule->setData('is_applied', true);
        } catch (RuntimeException $e) {
            Mage::logException($e);
        }
    }

    /**
     * @param $observer
     */
    public function adminhtmlBlockSalesruleActionsPrepareform($observer)
    {
        $field = $observer->getForm()->getElement('simple_action');
        $options = $field->getValues();
        $options[] = array(
            'value' => self::ADD_GIFT_ACTION,
            'label' => Mage::helper('gift')->__('Add a Gift')
        );
        $field->setValues($options);

        $fieldset = $observer->getForm()->getElement('action_fieldset');
        $fieldset->addField('gift_sku', 'text', array(
            'name' => 'gift_sku',
            'label' => Mage::helper('gift')->__('Gift SKU'),
            'title' => Mage::helper('gift')->__('Gift SKU'),
            'note' => Mage::helper('gift')->__('Enter the SKU of the gift'),
        ));

        $fieldset->addField('gift_custom_price', 'text', array(
            'name' => 'gift_custom_price',
            'label' => Mage::helper('gift')->__('Gift Custom Price'),
            'title' => Mage::helper('gift')->__('Gift Custom Price'),
            'note' => Mage::helper('gift')->__('Enter the custom price of the gift'),
        ));
    }

    /**
     * @param $observer
     * @throws Mage_Core_Exception
     */
    public function adminhtmlControllerSalesrulePrepareSave($observer)
    {
        $request = $observer->getRequest();
        if ($request->getParam('simple_action') == self::ADD_GIFT_ACTION) {
            $giftSku = $request->getParam('gift_sku');
            if (!$this->isValidGiftSku($giftSku)) {
                $data = $request->getPost();
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                throw new Mage_Core_Exception('The free product SKU must be a valid product.');
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function catalogProductTypePrepareFullOptions(Varien_Event_Observer $observer)
    {
        if ($observer->getBuyRequest()->getData('is_gift')) {
            $observer->getProduct()->setIsGift(true);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function salesQuoteProductAddAfter(Varien_Event_Observer $observer)
    {
        foreach ($observer->getEvent()->getItems() as $quoteItem) {
            $quoteItem->setIsGift($quoteItem->getProduct()->getIsGift());
        }
    }

    /**
     * @param $ruleId
     * @param $sku
     * @param $storeId
     * @param $qty
     * @return mixed
     */
    protected function getFreeQuoteItem($ruleId, $sku, $storeId, $qty)
    {
        if ($qty < 1) {
            throw new RuntimeException(sprintf(
                'Invalid Gift product qty. Rule ID: %d, Gift Qty: %d',
                $ruleId, $qty
            ));
        }

        $product = Mage::getModel('catalog/product');
        $product->setStoreId($storeId);
        $product->load($product->getIdBySku($sku));

        if ($product == false) {
            throw new RuntimeException(sprintf(
                'Gift product not found. Rule ID: %d, Gift SKU: %s, Store ID: %d',
                $ruleId, $sku, $storeId
            ));
        }

        Mage::getModel('cataloginventory/stock_item')->assignProduct($product);

        if ($product->isSalable() == false) {
            throw new RuntimeException(sprintf(
                'Gift product not saleable. Rule ID: %d, Gift SKU: %s, Store ID: %d',
                $ruleId, $sku, $storeId
            ));
        }

        $quoteItem = Mage::getModel('sales/quote_item')->setProduct($product);
        $quoteItem
            ->setQty($qty)
            ->setCustomPrice(0.0)
            ->setOriginalCustomPrice($product->getPrice())
            ->setIsGift(true)
            ->setWeeeTaxApplied('a:0:{}')
            ->setStoreId($storeId);

        $quoteItem->addOption(new Varien_Object(array(
            'product' => $product,
            'code' => 'info_buyRequest',
            'value' => serialize(array('qty' => $qty, 'is_free_product' => true))
        )));

        $quoteItem->addOption(new Varien_Object(array(
            'product' => $product,
            'code' => 'gift_uniqid',
            'value' => uniqid(null, true)
        )));

        return $quoteItem;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     */
    protected function setQuoteItemTaxPercent(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        $quote = $quoteItem->getQuote();

        $request = $taxCalculationModel->getRateRequest(
            $quote->getShippingAddress(),
            $quote->getBillingAddress(),
            $quote->getCustomerTaxClassId(),
            $quoteItem->getStore()
        );
        $rate = $taxCalculationModel->getRate(
            $request->setProductClassId($quoteItem->getProduct()->getTaxClassId())
        );
        $quoteItem->setTaxPercent($rate);
    }

    /**
     * @param $giftSku
     * @return bool
     */
    protected function isValidGiftSku($giftSku)
    {
        if (trim($giftSku) === '') {
            return false;
        }
        $skus = $this->getSkuList($giftSku);
        foreach ($skus as $sku) {
            if (! Mage::getModel('catalog/product')->getIdBySku($sku)) {
                return false;
            }
        }
        return true;
    }
}
