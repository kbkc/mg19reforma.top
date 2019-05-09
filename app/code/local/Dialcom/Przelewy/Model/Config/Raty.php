<?php

class Dialcom_Przelewy_Model_Config_Raty extends Mage_Core_Model_Config_Data {
	public function toOptionArray() {
		return array(
			array('value'=>2, 'label'=> Mage::helper('przelewy')->__('Product page (information) and payment page (button).')),
			array('value'=>1, 'label'=> Mage::helper('przelewy')->__('Only payment page (button).')),
			array('value'=>0, 'label'=> Mage::helper('przelewy')->__('Do not show the installment.')),
		);
	}
}
