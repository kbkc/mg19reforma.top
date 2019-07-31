<?php

class Dialcom_Przelewy_Model_Config_Product {
	public function toOptionArray() {

		$productCollection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('name')
			->addAttributeToFilter('type_id', array('eq' => 'virtual'))
			->load()
		;


		$result = array();
		foreach ($productCollection as $product) {
			$result[] = array('value'=>$product->getId(), 'label'=>$product->getName());
		}		
		return $result;
	}
}
