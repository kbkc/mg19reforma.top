<?php

class Dialcom_Przelewy_Block_Info_Przelewy extends Mage_Payment_Block_Info
{
    /** @var int */
    private $storeID;

    public function getDescription()
    {
        $this->storeID = Mage::helper("przelewy")->getStoreID();

        return Mage::getStoreConfig('payment/dialcom_przelewy/text',  $this->storeID);
    }

    public function getSpecificInformation()
    {
        $info = $this->getInfo()->_data['additional_information'];
        $ret = array();

        if (!empty($info['method_name'])) {
            $ret['Metoda'] = $info['method_name'];
        }
        if (!empty($info['cc_name'])) {
            $ret['Karta'] = $info['cc_name'];
        }

        if (isset($info['p24_forget'])) {
            Dialcom_Przelewy_Model_Recuring::setP24Forget($info['p24_forget'] == '1');
        }

        return $ret;
    }
}
