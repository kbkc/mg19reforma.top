<?php

class RedInGo_Kid_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        echo 33;
//        $resource = Mage::getSingleton('core/resource');
//        $writeConnection = $resource->getConnection('core_write');
//        $table = $resource->getTableName('sales_flat_order_grid');
//        $tab = $resource->getTableName('sales_flat_order');
//
//        $orderIds = $this->getRequest()->getPost('order_ids');
//        foreach($orderIds as $orderId) {
//            $order = Mage::getModel('sales/order')->load($orderId);
//            $order->setData('samanta_status', 0);
//            try{
//                $order->save();
//                $query = "UPDATE {$table} SET pc_status = '0' WHERE entity_id = ".(int)$order->getId();
//                $writeConnection->query($query);
//            } catch  (Exception $exc)  {
//
//            }
//			
//            try{
//                $order->save();
//                $query = "UPDATE {$tab} SET pc_status = '0' WHERE entity_id = ".(int)$order->getId();
//                $writeConnection->query($query);
//            } catch  (Exception $exc)  {
//
//            }
//        }
//
//        $this->_redirect('adminhtml/sales_order/index');
    }
}

