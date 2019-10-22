<?php

class RedInGo_Kid_Helper_Zamowienie extends Mage_Core_Helper_Abstract{

    private function add_xml($nazwa, $value, $dom, $zamowienie){
        $tmp = $dom->createElement($nazwa);
        $zamowienie->appendChild($tmp);
        $text = $dom->createTextNode( $value );
        $tmp->appendChild($text);
    }
    private function nazwa($order){
        $Id = $order->getBillingAddress()->getId();
        $address = Mage::getModel('sales/order_address')->load($Id);
        return $address->getCompany()?$address->getCompany():$order->getData('customer_firstname').' '.$order->getData('customer_lastname');
    }
    private function id_klienta($order){
        return $order->getCustomerId()?$order->getCustomerId():0;
    }
    private function limit($txt,$liimit = 500){
        return mb_substr($txt, 0, $liimit);
    }
    public function zamowienia(){

        $order_collection = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('kid',  array('neq' => 2));
                //->addAttributeToFilter('status', array('in' => array('processing')));

        echo "<pre>";
        $dom = new DOMDocument('1.0', 'utf-8');
        $root = $dom->createElement("zamowienia");
        $dom->appendChild($root);

        foreach ($order_collection as $order) {
            print_r( $order->getData() );
            $zamowienie = $dom->createElement("zamowienie");
            $root->appendChild($zamowienie);

            $id = $dom->createAttribute('id');
            $id->value = $order->getId() ;
            $zamowienie->appendChild($id);

            $numer = $dom->createElement("numer");
            $zamowienie->appendChild($numer);

            $text = $dom->createTextNode( $order->getData('increment_id') );

            $shipping_address = $order->getShippingAddress();
            $numer->appendChild($text);

            $path = "carriers/".explode('_', $order->getShippingMethod() )[0]."/title";

            $this->add_xml('paragon', $order->getData('customer_taxvat')?0:1, $dom, $zamowienie);
            $this->add_xml('waluta', $order->getData('order_currency_code'), $dom, $zamowienie);
            $this->add_xml('kurs', $order->getData('store_to_order_rate'), $dom, $zamowienie);
            $this->add_xml('id_klienta', ( $this->id_klienta($order)) , $dom, $zamowienie); // klijet
            $this->add_xml('nazwa_dostawy', Mage::getStoreConfig( $path ), $dom, $zamowienie);
            $this->add_xml('cena_dostawy_netto', $order->getData('shipping_amount'), $dom, $zamowienie);
            $this->add_xml('cena_dostawy_brutto', $order->getData('shipping_incl_tax'), $dom, $zamowienie);
            $this->add_xml('forma_platnosci', $order->getPayment()->getMethodInstance()->getTitle(), $dom, $zamowienie);
            $this->add_xml('status_platnosci', $order->getData('state'), $dom, $zamowienie);

            $adres_wysylki = $dom->createElement("adres_wysylki");
            $zamowienie->appendChild($adres_wysylki);

            if( is_object( $shipping_address ) ){
                $this->add_xml('imie', $this->limit( $shipping_address->getFirstname(),30 ), $dom, $adres_wysylki);
                $this->add_xml('nazwisko', $this->limit( $shipping_address->getLastname(),20 ), $dom, $adres_wysylki);
                $this->add_xml('adres', $this->limit( $shipping_address->getData('street') ), $dom, $adres_wysylki);
                $this->add_xml('kod_poczt',$this->limit( $shipping_address->getPostcode() ), $dom, $adres_wysylki);
                $this->add_xml('miejscowosc',$this->limit( $shipping_address->getCity()  ), $dom, $adres_wysylki);
                $this->add_xml('tel',$this->limit( $shipping_address->getTelephone()  ), $dom, $adres_wysylki);
            }
        }

        $dom->save("integrator/zamowienia.xml");

        $dom = new DOMDocument('1.0', "UTF-8");
        $root = $dom->createElement("kontrahenci");
        $dom->appendChild($root);
        foreach ($order_collection as $order) {
            $zamowienie = $dom->createElement("kontrahent");
            $root->appendChild($zamowienie);

            $id = $dom->createAttribute( 'id');
            $id->value = ( $this->id_klienta($order));
            $zamowienie->appendChild($id);

            $id_zam = $dom->createAttribute( 'id_zam');
            $id_zam->value = $order->getId();

            $this->add_xml('id_zam', (int)$order->getId() , $dom, $zamowienie);
            $this->add_xml('imie', $this->limit( $order->getData('customer_firstname') ), $dom, $zamowienie);
            $this->add_xml('nazwisko', $this->limit( $order->getData('customer_lastname'),30 ), $dom, $zamowienie);
            $this->add_xml('nazwa', $this->limit( $this->nazwa($order),20 ), $dom, $zamowienie);

            $address = Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
            $this->add_xml('adres', $this->limit($address->getData('street') ), $dom, $zamowienie);
            $this->add_xml('kod_poczt', $this->limit($address->getData('postcode') ), $dom, $zamowienie);
            $this->add_xml('miejscowosc',$this-> limit($address->getData('city') ), $dom, $zamowienie);
            $this->add_xml('email', $this->limit($order->getData('customer_email') ), $dom, $zamowienie);
            $this->add_xml('tel',$this->limit( $address->getData('telephone') ), $dom, $zamowienie);

            $shipping_address = $order->getShippingAddress();
            if( $order->getData('customer_taxvat') ){
                $this->add_xml('nip', $order->getData('customer_taxvat') , $dom, $zamowienie);
            }

        }
        $dom->save("integrator/kontrahenci.xml");

        $dom = new DOMDocument('1.0', "UTF-8");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $root = $dom->createElement("pozycje_zamowienia");
        $dom->appendChild($root);

        foreach ($order_collection as $order) {
            $x = $order->getData('base_discount_amount') ;
            $y = $order->getData('base_subtotal_incl_tax');

            $rm  = ($x / $y) ;
            $rm = abs( $rm );
            $items = $order->getAllVisibleItems();
            foreach ($items as $item) {
                $zamowienie = $dom->createElement("pozycja");
                $root->appendChild($zamowienie);

                $this->add_xml('id_zam', (int)$order->getId() , $dom, $zamowienie);
                $a = $dom->createElement("towar");
                $zamowienie->appendChild($a);

                $productid = Mage::getModel('catalog/product')->getIdBySku( $item->getSku() );
                $product = Mage::getModel('catalog/product')->load( $productid );

                $id_cs = $dom->createAttribute('id_cs');
                $id_cs->value = (int)$product->getId();
                $id_wf = $dom->createAttribute('id_wf');
                $id_wf->value = $product->wfmag() ;

                $a->appendChild($id_cs);
                $a->appendChild($id_wf);

                $this->add_xml('ilosc', (int)$item->getQtyOrdered() , $dom, $zamowienie);
                $this->add_xml('cena_netto', $item->getData('original_price') - $item->getData('original_price') * $rm, $dom, $zamowienie);
                $this->add_xml('cena_brutto', $item->getData('price_incl_tax')  - $item->getData('price_incl_tax') * $rm, $dom, $zamowienie);
                //$this->add_xml('rn', $rm , $dom, $zamowienie);

                if( $product->jedn() ){
                    $this->add_xml('jedn', $product->getAttributeText('jed') , $dom, $zamowienie);
                }
            }
        }
        $dom->save("integrator/pozycje_zamowienia.xml");
         foreach ($order_collection as $order) {
             $order->setKid(1);
             $order->save();
         }
    }
    public function statusySklep(){
      echo "<pre>";

      $dom = new DOMDocument('1.0', "UTF-8");
      $root = $dom->createElement("pozycje_zamowienia");
      $dom->appendChild($root);

      $order_collection = Mage::getModel('sales/order')->getCollection()
        ->addAttributeToFilter('status', array('nin' => array('complete', 'canceled') ) );

      foreach ( $order_collection as $order ) {
        $zam = $dom->createElement("zam");
        $root->appendChild($zam);

        $id = $dom->createAttribute('id');
        $id->value = $order->getId();
        $status = $dom->createAttribute('s');
        $status->value = $order->getData(status) ;

        $zam->appendChild($id);
        $zam->appendChild($status);

        print_r( $order );
      }

      $dom->save("integrator/statusy_zam_sklep.xml");
    }
    private function  comment($value, $comment, $xml, $text )
      {
      if( $value[ $xml ] )
      {
        if( $comment != "" )
        {
           $comment.= "<br>";
        }
        $comment .= $this->__( $text ).$value[ $xml ];
      }
      return $comment;
    }
    public function  status(){
        echo '<pre>';
        $zam = Mage::helper('kid/xml')->status();
        print_r($zam);

        foreach ($zam as $value) {
            echo $value['s']."<br>";
            print_r( $value );
            $order = Mage::getModel('sales/order')->load( $value['id'] );
            $collection = Mage::getModel('kid/status')->getCollection()
                ->addFieldToFilter('kid_status', $value['s'] )
                ->getFirstItem();

            print_r( $collection->getData() );
            if( !$order->getId() ||  !$order->getData('increment_id') || !$collection->getData())
            {
              echo "ERROR !!<br>";
              continue;
            }
            echo $order->getData('increment_id').'<br>';
            $state = $collection['status'];
            $status = $collection['status'];
            $comment = '';
            $comment = nl2br( $collection['opis'] );

            $comment = $this->comment($value, $comment, 'f', "Invoice: " );
            $comment = $this->comment($value, $comment, 'np', "Supply: " );

            echo $comment;
            echo "string";
            $order->setState($state, $status, $comment, $collection['wyslij'] );
            $order->setKid(2);
            $order->save();
            $order->sendOrderUpdateEmail(true, $comment);

            if( $collection['faktura'] ){
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                if ( $invoice->getData('subtotal') )
                {
                  $invoice->register();
                  $invoice->getOrder()->setCustomerNoteNotify(false);
                  $invoice->getOrder()->setIsInProcess(true);
                  $invoice->sendEmail();
                  $order->addStatusHistoryComment('Faktura Enova.', false);
                  $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                  try {
                    $transactionSave->save();
                  } catch (Exception $e) {
                    echo $e;
                  }
                }
            }

            if( $collection['dostawa'] ){
                $shipment = $order->prepareShipment();
                $shipment->register();
                $order->setIsInProcess(true);
                $order->addStatusHistoryComment('Dostawa Enova', false);
                try {
                  $transactionSave = Mage::getModel('core/resource_transaction')
                      ->addObject($shipment)
                      ->addObject($shipment->getOrder())
                      ->save();
                } catch (Exception $e) {
                  echo $e;
                }
            }
        }
    }
    public function faktury()
    {
      echo "<pre>";
      $resource = Mage::getSingleton('core/resource');
      $writeConnection = $resource->getConnection('core_write');
      $dir   = Mage::getBaseDir().DS.'integrator';
      $files = scandir($dir);
      foreach ($files as $file) {
        $pos = strpos($file, 'faktury_pdf_');
        if ($pos !== false) {
            $xml = Mage::helper('kid/xml')->faktury( $file );
            foreach ($xml as $value) {
              print_r( $value );
              $order = Mage::getModel('sales/order')->loadByIncrementId( $value['nr_zam'] );
              $value[ 'mg_id_customer' ] = $value[ 'mg_id_customer' ] = $order->getData( 'customer_id' );
              Mage::helper('base/sql')->insert('redingo_kid_faktory', $value );
            }
        }
      }


      echo 1;
    }
}
