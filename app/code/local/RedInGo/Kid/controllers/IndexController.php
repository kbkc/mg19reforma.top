<?php
class RedInGo_Kid_IndexController extends Mage_Core_Controller_Front_Action
{
    public function IndexAction() {
        error_reporting(E_ALL ^ E_NOTICE);
        error_reporting(-1);
        $go = Mage::app()->getRequest()->getParam('go');
        switch ($go) {
            case 'art':
                echo "<pre>";
                if( Mage::getStoreConfig('kid/kid/wylocz')){
                    Mage::helper('kid/sql')->off();
                }
                //Mage::helper('kid/kid')->katrgoria();
                Mage::helper('kid/produkt')->import();
                //Mage::helper('kid/produkt')->image();
                //Mage::helper('kid')->websites();
                if( Mage::getStoreConfig('kid/reindex/wlac') )
                {
                  Mage::helper('kid/produkt')->reindex( explode(',', Mage::getStoreConfig('kid/reindex/reindex') ) );
                }
                break;
            case 'ceny_stany':
                Mage::helper('kid/produkt')->importSzybki();
                if( Mage::getStoreConfig('kid/reindex/import_szybki') )
                {
                  Mage::helper('kid/produkt')->reindex( array(2, 8) );
                }
                break;
            case 'art_sklep':
                Mage::helper('kid/kid')->eksport();
                break;
            case 'zam':
                Mage::helper('kid/zamowienie')->zamowienia();
                break;
            case 'statusy_sklep':
                Mage::helper('kid/zamowienie')->statusySklep();
                break;
            case 'stats':
                Mage::helper('kid/zamowienie')->status();
                break;
            case 'faktury':
                echo faktury;
                Mage::helper('kid/zamowienie')->faktury();
                break;
            case 'grupy_cenowe':
                Mage::helper('kid/produkt')->grupyCenowe();
                break;
            case 'kontrahenci_grupy':
                Mage::helper('kid/produkt')->kontrahenciGrupy();
                break;
            case 'kat':
                 Mage::helper('kid/kid')->kategorie2() ;
                break;
            case 'dew':
                 print_r(Mage::getStoreConfig('kid') );
                 break;
            default:
                echo "EksporArt:1|ImportArt:1|EksportZdj:1|AktualizacjaCenStanow:1|ImportZam:1|AktualizacjaZam:1|Kompresja:0|Wersja:2";
        }
    }
}
