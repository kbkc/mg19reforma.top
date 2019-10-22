<?php

class RedInGo_Kid_Helper_Xml extends Mage_Core_Helper_Abstract{
    private function vat($vat){
        switch ($vat) {
            case 0:
                return Mage::getStoreConfig('kid/tax/tax1');
            case 5:
                return Mage::getStoreConfig('kid/tax/tax2');
             case 8:
                return Mage::getStoreConfig('kid/tax/tax3');
            case 23:
                return Mage::getStoreConfig('kid/tax/tax4');
            default:
                return 0;
        }
    }
    private function pola($value, $p){
        for ($i = 1; $i < 11; $i++) {
            $tmp = 'p'.$i;
            $pole = (string)$value->{$tmp}[0];
            if( Mage::getStoreConfig('kid/pole/p'.$i) ){
                $p[Mage::getStoreConfig('kid/pole/p'.$i)] = $pole;
            }
            if( $pole ){
                $p['pola']['p'.$i] = $pole;
            }
        }
        return $p;
    }
    private function aktualizacja($produkt, $nazwa){
        if(Mage::getStoreConfig('kid/aktualizacja/'.$nazwa)){
            $produkt[$nazwa] = $produkt[$nazwa] ;
        } else {
            if( !Mage::getSingleton('catalog/product')->getIdBySku( $produkt['sku'] ) ) { // model
                $produkt[$nazwa] = $produkt[$nazwa] ;
            } else{
                $produkt[$nazwa] = "";
            }
        }
        return $produkt;
    }
    private function opis($produkt, $nazwa, $p){
        if(Mage::getStoreConfig('kid/aktualizacja/'.$nazwa)){
            $p[Mage::getStoreConfig('kid/produkt/'.$nazwa)] = $produkt[$nazwa] ;
        } else {
            if( !Mage::getSingleton('catalog/product')->getIdBySku( $produkt['sku'] ) ) { // model
                $p[Mage::getStoreConfig('kid/produkt/'.$nazwa)] = $produkt[$nazwa] ;
            }
        }
        return $p;
    }
    public function ceny_stany(){
        $xml=simplexml_load_file("integrator/ceny_stany.xml");
        $towary = $xml->art;
        $produkt =array();
        $i = 0;
        foreach ($towary as $value) {
            $produkt[$i]['id'] = (int)$value['id'];
            $produkt[$i]['s'] = (string)$value->s;
            $produkt[$i]['cena'] = (float)$value->cd['n'];
            $ceny = $value->ca->c;
            $j = 0;
            foreach ($ceny as $cena) {
                $produkt[$i]['ceny'][(int)$cena['idc']]['n'] =(float)$cena['n'];
                $produkt[$i]['ceny'][(int)$cena['idc']]['b'] =(float)$cena['b'];
                $j++;
            }
            $i++;
        }
        return $produkt;
    }
    public function art(){
        $cenyArtykulow = $this->cenyArtykulow();
        $kat = Mage::helper('kid')->kat();
    		$doc=new DOMDocument();
    		$doc->load(Mage::getBaseDir().DS.'integrator'.DS."artykuly.xml");
    		// if( !$doc->schemaValidate( Mage::helper('kid')->dir_xls."artykuly.xsd") )
        // {
        //   return false;
        // };

        $xml=simplexml_load_string( $doc->saveXML() );
        $towary = $xml->art;
        $produkt =array();
        $i =0;
        foreach ($towary as $value) {
            switch ( Mage::getStoreConfig('kid/kid/sku')) { // kat --  katalogowy han -- handlowy
                case "kat":
                    $produkt[$i]['sku'] = (string)$value->kat[0];
                    break;
                case "id":
                    $produkt[$i]['sku'] = (string)$value['id'];
                    break;
                default:
                    $produkt[$i]['sku'] = (string)$value->kat[0];
                    break;
            }

            $produkt[$i]['name'] = (string)$value->nazwa[0];
            if(  Mage::getStoreConfig('kid/kid/name2') ) {
                $produkt[$i]['name'] .= ' '.(string)$value->nazwa2[0];
            }

            //
            if( Mage::getStoreConfig('kid/kid/z_pol')){
                $nazwa = array();
                for ($j = 1; $j < 10; $j++) {
                    $tmp = 'p'.$j;
                    $nazwa[] = trim( (string)$value->{$tmp}[0] );
                }
                $produkt[$i]['name'] = implode(' ', $nazwa);
            }
            //

            if( (string) $value->cn_dom_jed[0] ){
                $produkt[$i]['cena_n'] =(string) $value->cn_dom_jed[0];
            } else {
                $produkt[$i]['cena_n'] =(string) $value->cena_n[0];
            }

            /*
             * cena
             */
            $typ_pric = "cena_".Mage::getStoreConfig('kid/kid/cena');

            $produkt[$i]['price'] =(string)$value->{$typ_pric}[0];
            if( (string) $value->stan_dom_jed[0] ){
                $produkt[$i]['qty'] = (string)$value->stan_dom_jed[0];
            } else {
                $produkt[$i]['qty'] = (string)$value->stan[0];
            }
            $s = $value->stan[0]->s;
            foreach ($s as $s_value) {
                $produkt[$i]['qty'] += $s_value;
            }
            $produkt[$i]['description'] =(string) $value->opis[0];
            $produkt[$i]['short_description'] =(string) $value->uwagi[0];
            //$produkt[$i]['producent'] =(string) $value->prod[0];
            $produkt[$i]['waga'] = (string)$value->waga[0];
            $produkt[$i]['wys'] =(string) $value->wys[0];
            $produkt[$i]['wyroz'] =(string) $value->wyroz[0];
            $produkt[$i]['szer'] =(string) $value->szer[0];
            $produkt[$i]['gleb'] =(string) $value->gleb[0];

            $produkt[$i]['jed']  = "szt.";
            if( $value->jed[0] ){
                $produkt[$i]['jed'] =(string) $value->jed[0];
            }
            $produkt[$i]['han'] =(string) $value->han[0];
            $produkt[$i]['kat'] =(string) $value->kat[0];
            $produkt[$i]['id'] = (string)$value['id'];
            $produkt[$i]['tax_class_id'] = $this->vat( (string)$value->vat[0] );
            $produkt[$i]['kod_kreskowy'] = (string)$value->kod_kreskowy[0];

            $produkt[$i]['weight'] = 1;
            $dom_jed = (string)$value->dom_jed[0];
            $produkt[$i]['j'] = $value->jed_inne[0] ;

            $jed = array();

            if( Mage::getStoreConfig('kid/aktualizacja/kategori') ) {
                if( Mage::getStoreConfig('kid/kid/kategoria') ){
                    $produkt[$i]['category_ids'] =$kat[(string)$value->p1[0]];
                } else {
                    $produkt[$i]['category_ids'] =(int) $value->id_kat_tree[0];
                }
            }

            $produkt[$i]['promocja'] = (string)$value->cena_prom_n[0];
            $produkt[$i]['special_from_date'] = (string)$value->prom_od[0];
            $produkt[$i]['special_to_date'] = (string)$value->prom_do[0];
            $produkt[$i] = $this->pola($value, $produkt[$i]);
            $produkt[$i] = $this->aktualizacja($produkt[$i],'name');
            $produkt[$i] = $this->opis($value,'description', $produkt[$i]);
            $produkt[$i] = $this->opis($value,'short_description', $produkt[$i]);
            foreach ($produkt[$i] as $key => $value) {
                if(  $value == ""){
                    unset(  $produkt[$i][ $key ] );
                }
            }
           $i++;
        };
        return $produkt;
    }
    public function status(){
        $xml=simplexml_load_file("integrator/zamowienia_statusy.xml");
        $zam = $xml->zam;
        $produkt =array();
        $i =0;
        foreach ($zam as $value) {
            $produkt[$i]['s'] = (string)$value->s[0];
            $produkt[$i]['f'] = (string)$value->f[0];
            $produkt[$i]['np'] = (string)$value->np[0];

            $produkt[$i]['id'] = (string)$value['id'];
            $i++;
        };
        return $produkt;
    }
    public function kategorie(){
        return $this->getKategorieKat();
    }
    private function getKategorieKat(){
        $xml=simplexml_load_file("integrator/kategorie.xml");
        $zam = $xml->kat;
        $produkt =array();
        $i =0;
        foreach ($zam as $value) {
            $produkt[$i]['nazwa'] = (string)$value->nazwa[0];
            $produkt[$i]['id'] = (string)$value['id'];
            $i++;
        };
        return $produkt;
    }
    public function grupyCenowe(){
        $xml=simplexml_load_file("integrator/ceny_grupy.xml");
        $grupa_xml = $xml->a;
        $grupa =array();
        foreach ($grupa_xml as $value) {
            $j = 0;
            $i = (string)$value['idx'];
            foreach ($value->g as $g) {
                $grupa[$i][$j]['grupa'] = (string)$g['idg'];
                $grupa[$i][$j]['cena'] = (string)$g['n'];
                $j++;
            }
        };
        return $grupa;
    }
    public function grupy(){
        $xml=simplexml_load_file("integrator/grupy_cenowe.xml");
        $grupa_xml = $xml->grupa;
        $grupa =array();
        $i =0;
        foreach ($grupa_xml as $value) {
            $grupa[$i]['id'] = (string)$value['id'];
            $grupa[$i]['n'] = (string)$value['n'];
            $i++;
        };
        return $grupa;
    }
    public function kontrahenciGrupy(){
        $xml=simplexml_load_file("integrator/kontrahenci_grupy.xml");
        $grupa =array();
        $i = 0;
        foreach ($xml->k as $value) {
            $grupa[$i]['id'] = (string)$value['id'];
            $grupa[$i]['ids'] = (string)$value['ids'];
            $grupa[$i]['idg'] = (string)$value['idg'];
            $grupa[$i]['n'] = (string)$value['n'];
            $grupa[$i]['nip'] = (string)$value['nip'];
            $grupa[$i]['email'] = (string)$value['email'];
            $grupa[$i]['imie'] = (string)$value['imie'];
            $grupa[$i]['nazwisko'] = (string)$value['nazwisko'];
            $grupa[$i]['adres'] = (string)$value['adres'];
            $grupa[$i]['kod'] = (string)$value['kod'];
            $grupa[$i]['miejscowosc'] = (string)$value['miejscowosc'];
            $grupa[$i]['kraj'] = (string)$value['kraj'];
            $grupa[$i]['tel'] = (string)$value['tel'];
            $grupa[$i]['tel_kom'] = (string)$value['tel_kom'];
            $i++;
        }
        //print_r($grupa);
        return $grupa;
    }
    public function cenyArtykulow(){
        $xml=simplexml_load_file("integrator/ceny_artykulow.xml");
        $tmp = array();
        foreach ($xml->lista_cen->c as $c) {
            $tmp[ (string)$c['ida'] ][ (string)$c['idc' ] ]['n'] = (string)$c['n'];
            $tmp[ (string)$c['ida'] ][ (string)$c['idc' ] ]['b'] = (string)$c['b'];

        }

        return $tmp;
    }
    public function faktury( $xml )
    {
      $tmp = array();
      $xml=simplexml_load_file("integrator".DS.$xml);
      $i = 0;
      foreach ( $xml as $item )
      {
        $tmp[$i]['nr']       = (string)$item->nr;
        $tmp[$i]['nr_zam']   = (string)$item->nr_zam;
        $tmp[$i]['id_kontr'] = (string)$item->id_kontr;
        $tmp[$i]['w_brutto'] = (string)$item->w_brutto;
        $tmp[$i]['data_wystawienia'] = (string)$item->data_wystawienia;
        $tmp[$i]['plik']     = (string)$item->plik;
        $i++;
      }
      return $tmp;
    }
}
