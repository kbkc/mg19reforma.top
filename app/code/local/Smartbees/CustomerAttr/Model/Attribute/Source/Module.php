<?php
class Smartbees_CustomerAttr_Model_Attribute_Source_Module extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();
            foreach(Mage::getStoreConfig('registrationpoll_options/registrationpoll_answers') as $x=>$x1)
            {
                if($x1=='') continue;                                 
                $this->_options[] = array(
                        'value' => $x1,
                        'label' =>  $x1
                );
            }
        }
        return $this->_options;
    }
}