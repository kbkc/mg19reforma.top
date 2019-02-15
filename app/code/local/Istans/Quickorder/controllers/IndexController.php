<?php

class Istans_Quickorder_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->getLayout()->getBlock("head")->setTitle($this->__("Quick order form"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("quick order form", array(
            "label" => $this->__("Quick order form"),
            "title" => $this->__("Quick order form")
        ));

        $this->renderLayout();
    }


    public function addToCartAction()
    {
        if ($data = $this->getRequest()->getPost('product')) {
            $cart   = Mage::getSingleton('checkout/cart');
            try {
                foreach ($data as $row) {
                    if (!isset($row['sku']) || !isset($row['qty']) || $row['qty'] < 1) {
                        continue;
                    }
                    $productId = Mage::getModel('catalog/product')->getIdBySku($row['sku']);
                    if ($productId) {
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId(Mage::app()->getStore()->getId())
                            ->load($productId);
                        $attributeName = [];
                        if (!empty($row['super_attribute'])) {
                            foreach ($row['super_attribute'] as $key => $superAtt) {
                                $attributeName += [$key => $superAtt];
                            }
                        }

                        $params = ['cart'            => 'add',
                            'product'         => $productId,
                            'related_product' => '',
                            'super_attribute' => $attributeName,
                            'qty'             => !isset($row['qty']) ? 1: $row['qty']];

                        $this->getRequest()->setparam('qty', $params['qty']);

                        $cart->addProduct($product, $params);
                        if (!empty($params['related_product'])) {
                            $cart->addProductsByIds(explode(',', $params['related_product']));
                        }

                        $cartStockMessage = Mage::app()->getResponse()->getBody();
                        if (!empty($cartStockMessage)) {
                            Mage::getSingleton('checkout/session')->addNotice($cartStockMessage);
                            Mage::app()->getResponse()->setBody('');
                        } else {
                            $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
                            Mage::getSingleton('checkout/session')->addSuccess($message);
                        }
                    }

                }

                //Cart Save needs to be the last
                $cart->save();
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

                //Check all cart and update out of stocks
                foreach ($data as $row) {
                    $productId = Mage::getModel('catalog/product')->getIdBySku($row['sku']);
                    $product = Mage::getModel('catalog/product')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($productId);

                    $attributeName = [];
                    if (!empty($row['super_attribute'])) {
                        foreach ($row['super_attribute'] as $key => $superAtt) {
                            $attributeName += [$key => $superAtt];
                        }
                    }

                    Mage::dispatchEvent('checkout_cart_add_product_complete',
                        array(
                            'product' => $product,
                            'request' => $this->getRequest(),
                            'response' => $this->getResponse(),
                            'attribute' => $attributeName)
                    );
                }

            }
            catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        Mage::getSingleton('checkout/session')->addError($message);
                    }
                }
            }
            catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
            }
        }

        $this->_redirect('checkout/cart');
    }

    public function productsAction()
    {
        $attributeMap = array(
            0   => 'sku',
            1   => 'name',
            2   => 'in_stock',
            3   => 'price'
        );
        $limit = (int)$this->getRequest()->getParam('length');
        $offset = (int)$this->getRequest()->getParam('start');
        $curPage = (($offset / $limit) + 1);
        $draw = $this->getRequest()->getParam('draw');
        $order = $this->getRequest()->getParam('order');
        $orderAttribute = $attributeMap[$order[0]['column']];
        $orderDir = strtoupper($order[0]['dir']);
        $search = $this->getRequest()->getParam('search');

        /**
         * @var $collection Mage_Catalog_Model_Resource_Product_Collection
         */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
            $collection->joinField('in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        $countProducts = $this->_getCountIds($collection);
        if (!empty($search['value'])) {
            $collection->addAttributeToFilter(
                array(
                    array('attribute' => 'sku', 'like' => '%' . $search['value'] . '%'),
                    array('attribute' => 'name', 'like' => '%' . $search['value'] . '%')
                )
            );
        }
        $collection->addAttributeToSort($orderAttribute, $orderDir);
        $collection->setPageSize($limit)
            ->setCurPage($curPage)
        ;

        $countFilteredProducts = $this->_getCountIds($collection);
        Mage::register('dontUseItDelightObserver', true);
        $productCollection = $collection->getItems();
        $productsData = array();
        /**
         * @var $block Istans_Quickorder_Block_Index
         */
        $block = Mage::getSingleton('core/layout')->createBlock('quickorder/index');
        foreach ($productCollection as $product) {
            $productAdditionalData = array(
                'add_to_cart_url'   => $block->getAddToCartUrl($product),
                'available'         => ($product->getQty() > 0 && $product->getInStock()) ? true : false,
                'product_price'     => number_format($product->getFinalPrice(), 2),
                'total'             => 0,
                'max_qty'           => (int)$product->getQty(),
                'product_url'       => $product->getProductUrl(),
                'name'              => $block->escapeHtml($product->getName()),
                'sku'               => $block->escapeHtml($product->getSku())
            );
            $productsData[] = array_merge($product->getData(), $productAdditionalData);
        }

        $response = array(
            'draw'              => $draw,
            "recordsTotal"      => $countProducts,
            "recordsFiltered"   => $countFilteredProducts,
            'data'              => $productsData
        );

        Mage::helper('core')->jsonEncode($response);
        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    protected function _getCountIds($collection)
    {
        $idsSelect = clone $collection->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);

        $idsSelect->columns($collection->getResource()->getIdFieldName(), 'e');
        $countProducts = count($collection->getConnection()->fetchCol($idsSelect));

        return $countProducts;
    }

    public function addtoquickorerAction()
    {
        /**
         * @var $block Istans_Quickorder_Block_Index
         */
        $block = Mage::getSingleton('core/layout')->createBlock('quickorder/index');
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));
        $addToCartUrl = $block->getAddToCartUrl($product);

        return $addToCartUrl;
    }

    public function quickordertocartAction()
    {
        $products = $this->getRequest()->getParam('product');
        $cart = Mage::getSingleton('checkout/cart')->init();
        $responseText = '';
        try {
            foreach ($products as $productId => $productData) {
                $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
                if ($product->getId()) {
                    if (($product->getTypeId() == 'simple' && !($product->getRequiredOptions())) || ($product->getTypeId() == 'virtual' && !($product->getRequiredOptions()))) {
                        if (!array_key_exists('qty', $productData)) {
                            $params['qty'] = $product->getStockItem()->getMinSaleQty();
                        } else {
                            $params['qty'] = $productData['qty'];
                        }
                        $cart->addProduct($product->getId(), $params);
                        unset($product);
                    }
                }
            }
            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            if (!$cart->getQuote()->getHasError()){
                $responseText = $this->_getAddToCartResponse();
            }
        } catch (Exception $e) {
            $responseText = $this->_getAddToCartResponse($e->getMessage());
            Mage::logException($e);
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseText));
    }

    protected function _getAddToCartResponse($errorMessage = '')
    {
        if ('' === $errorMessage) {
            $response['error'] = false;
            $response['body'] = $this->_getUpdateMiniCart();
        } else {
            $response['error'] = true;
            $response['body'] = $errorMessage;
        }

        return $response;
    }

    protected function _getUpdateMiniCart()
    {
        $miniCart = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar', 'cart_sidebar')
            ->setTemplate('magiccart/magicshop/checkout/cart/mini_cart.phtml');
        if($miniCart) {
            return $miniCart->toHtml();
        }

        return false;
    }
}