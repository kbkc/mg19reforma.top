<?php
class RedInGo_Kid_DewController extends Mage_Core_Controller_Front_Action{

    public function IndexAction() {

          echo Mage::helper('kid')->dir_xls;
          // 
        // echo "<pre>";
        // $dir = 'integrator';
        // if( $xml=simplexml_load_file( Mage::getBaseDir().DS.$dir.DS."artykuly.xml" ) ){
        //     print_r($xml);
        // } else {
        //     mail('r@redingo.pl', 'Error', "XML");
        // }
    }
}
