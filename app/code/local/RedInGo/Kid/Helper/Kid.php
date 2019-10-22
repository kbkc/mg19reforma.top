<?php

class RedInGo_Kid_Helper_Kid extends Mage_Core_Helper_Abstract{

    private function add_xml($nazwa, $value, $dom, $zamowienie){
        $tmp = $dom->createElement($nazwa);
        $zamowienie->appendChild($tmp);
        $text = $dom->createTextNode( $value );
        $tmp->appendChild($text);
    }

    public function eksport(){
        $collectionConfigurable = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToFilter('type_id', array('eq' => 'simple'))
                ->addAttributeToSelect('name, sku');

        $dom = new DOMDocument('1.0', 'utf-8');
        $artykuly = $dom->createElement("artykuly_sklep");
        $dom->appendChild($artykuly);

        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $produkt = Mage::getModel('base/produkt');
        $store = Mage::app()->getStore('default');
        $request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
        foreach ($collectionConfigurable as $value) {
            //print_r( $value->getdata() );
            $art = $dom->createElement("art");
            $id = $dom->createAttribute('id');
            $id->value = $value->getData('entity_id') ;
            $art->appendChild($id);
            $artykuly->appendChild($art);

            $nazwa = $produkt->nazwa( $value->getData('entity_id')  );
             echo  $value->getData('entity_id').'<br>';
             echo  $nazwa.'<br>';
            $cena = $produkt->cena( $value->getData('entity_id')  );
            $tax = $produkt->tax( $value->getData('entity_id')  );

            $n =  substr($nazwa,0, 40);
            $ncd = substr($nazwa,40, 40);

            $this->add_xml('n', $n , $dom, $art);
            $this->add_xml('ncd', $ncd , $dom, $art);

            $percent = (int)Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId( $tax ));
            $this->add_xml('vat', $percent , $dom, $art);

            if( Mage::getStoreConfig( 'tax/calculation/price_includes_tax' ) ){
                echo ($tax/100).'<br>';
                $cena_tmp =  $cena - ($tax/100)*$cena;

                $this->add_xml('cn', $cena_tmp , $dom, $art);
                $this->add_xml('cb', $cena , $dom, $art);
            } else {
                $cena_tmp =  $cena + ($tax/100)*$cena;

                $this->add_xml('cn', $cena , $dom, $art);
                $this->add_xml('cb', $cena_tmp , $dom, $art);
            }
            $this->add_xml('han', $value->getSku() , $dom, $art);
            $this->add_xml('kat', $value->getSku() , $dom, $art);

            unset($value);
        }
        $dom->save("integrator/artykuly_sklep.xml");
    }
}
