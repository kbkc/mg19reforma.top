<?php
class RedInGo_Kid_Model_Cron{	
    public function grupa(){
        $grupa = Mage::getModel('kid/grupa')->getCollection();
        foreach ($grupa as $item) {
            $id = $item->getId();
            $model = Mage::getModel("kid/grupa")->load($id);
            $model->generoj();
        }
    } 
}