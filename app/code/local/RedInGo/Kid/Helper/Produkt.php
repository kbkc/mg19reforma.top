<?php
class RedInGo_Kid_Helper_Produkt extends Mage_Core_Helper_Abstract{
    public function image(){
        return true;
        $dp = Mage::helper('kid')->magmi();
        echo get_class( $dp );
        $dir = Mage::getBaseDir().DS .'integrator'.DS.'zdjecia'. DS;
        $folder = dir($dir);
        $pliki =array();
        while ($plik = $folder->read()) {
            if (($plik != '.') AND ($plik != '..')) {
                $nazwa = pathinfo($plik);
                $pliki[] = $nazwa['basename'];
            }
        }
        foreach ($pliki as $plik) {
            $tmp = explode( '_', $plik );
            $sku = Mage::helper('kid/sql')->sku( $tmp[0] ) ;
            $img = $dir.$plik;
            $p=array(
                "sku"=>$sku,
                "image" =>$img,
                "small_image" =>$img,
                "thumbnail" =>$img,
                "media_gallery"=>$img,
                "media_gallery_reset" => 1,
            );
            $dp->ingest($p);
        }
        $dp->endImportSession();
    }
    public function sku($id){
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = 'SELECT * FROM ' . $resource->getTableName('kid/kid').' '
                . ' WHERE wfmag = '. (int)$id;
        $results = $readConnection->fetchAll($query);
        unset($resource);
        unset($readConnection);
        flush();
     return $results[0]['sku'];
    }
    public function import( $websites = NULL ){
        //$dp = Mage::helper('kid')->magmi();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');

        require_once("lib/magmi/inc/magmi_defs.php");
        require_once("lib/magmi/integration/inc/magmi_datapump.php");
        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("Default","create");

        $produkt = Mage::helper('kid/xml')->art( $websites );
        foreach ($produkt as $item) {
            $item['store'] = $websites;
            unset($item['pola']);
            unset($item['j']);
            if( Mage::getSingleton('catalog/product')->getIdBySku($item['sku'] ) )
            {
              echo "<hr>".$item['sku']."<hr>";
              foreach (Mage::getStoreConfig('kid/import') as $key => $value)
              {
                if( !$value )
                {
                  unset( $item[$key] );
                }
              };
            } else
            {
              $item['status'] =  Mage::getStoreConfig('kid/produkt/aktywny');
            }
            echo "<hr>";
            print_r( $item );
            $dp->ingest($item);
            $dp->endImportSession();
            $date = new DateTime();

            $tmp = array(
                'sku' => $item['sku'],
                'wfmag' => $item['id'],
            );

            echo Mage::helper('base/sql')->insert('redingo_kid', $tmp);
            unset($tmp);

        }
        $dp->endImportSession();
        echo ':)t';

    }
    public function importSzybki(){
        $p = Mage::helper('kid/xml')->ceny_stany();
        require_once("lib/magmi/inc/magmi_defs.php");
        require_once("lib/magmi/integration/inc/magmi_datapump.php");
        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("Default","create");
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $item = array();
        if( !Mage::getStoreConfig('kid/import/price') AND !Mage::getStoreConfig('kid/import/qty') )
        {
          return false;
        }
        echo "<pre>";
        foreach ($p as $value) {
            $sku = $this->sku($value['id']);
            if( !$sku )
            {
              continue;
            }
            $item=array(
              "sku"=> $sku,
              "price"=>$value['cena'],
              "qty" => $value['s']
            );
            foreach ( $value['ceny'] as $key => $cena) {
              $group_price = $readConnection->fetchOne(
                              "SELECT `customer_group_code`
                              FROM `customer_group`
                              WHERE `customer_group_kid` = $key
                              limit 1");
              if( $group_price )
              {
                $item[ 'group_price:'.$group_price ] = $cena[ Mage::getStoreConfig('kid/kid/cena') ];
              }
            }
            print_r( $item );
            $dp->ingest($item);
        };
        $dp->endImportSession();
        echo 'koniec';
    }
    public function grupyCenowe(){
        echo "<pre>";
        require_once("lib/magmi/inc/magmi_defs.php");
        require_once("lib/magmi/integration/inc/magmi_datapump.php");
        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("Default","create");

        $grupay1 = Mage::helper('kid/xml')->grupy();
        print_r( $grupay1 );
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_read');

        foreach ($grupay1 as $value1) {
           echo $query = "
                INSERT INTO `".$resource->getTableName('customer_group')."` (
                     `customer_group_id` ,
                     `customer_group_code` ,
                     `tax_class_id` ,
                     `customer_group_kid`
                     )
                     VALUES (
                     NULL , '".$value1['n']."', '3', ".$value1['id']."
                     ) ON DUPLICATE KEY UPDATE `customer_group_kid` = ".$value1['id'].";";
           try {
             $writeConnection->query($query);
           } catch (Exception $e) {
           }
        }

        $grupay = Mage::helper('kid/xml')->grupyCenowe();
        echo "<pre>";
        $tmp = array();
        foreach ($grupay as $key_g => $value) {
            $query = "
            SELECT e.sku
            FROM  `".$resource->getTableName('catalog_product_entity')."` e
            WHERE  e.sku = '".$key_g."';";
            $sku = $readConnection->fetchAll($query);
            $tmp[ 'sku' ] = $sku[ 0 ][ 'sku' ];
            foreach ($value as $j) {
                $query = "
                SELECT `customer_group_code`
                FROM `".$resource->getTableName('customer_group')."`
                WHERE `customer_group_kid` = ".$j['grupa']." limit 1;";
                $results = $readConnection->fetchAll($query);
                $tmp[ 'tier_price:'.$results[ 0 ][ 'customer_group_code' ] ] = $j['cena'];
            }
            print_r( $tmp );
            $dp->ingest($tmp);
            $dp->endImportSession();
        }
    }
    public function kontrahenciGrupy(){
        echo "<pre>w";
        $klieci = Mage::helper('kid/xml')->kontrahenciGrupy();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $customer = Mage::getModel("customer/customer");
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        $customer->setWebsiteId( $websiteId );

        foreach($klieci as $kliet){
            if( !$kliet['email'] ) continue;
            $customer->loadByEmail( $kliet['email'] );
            if ( !$customer->getData( 'entity_id' ) )
            {
              $customer = Mage::getModel("customer/customer");
              $customer->setWebsiteId($websiteId)
                       ->setStore($store)
                       ->setFirstname($kliet['imie'])
                       ->setLastname($kliet['imie'])
                       ->setEmail($kliet['email'])
                       ->setPassword('password');
              $customer->save();
            } else
            {
              continue;
            }
            $customer_group_id = $readConnection->fetchOne(
                            "SELECT `customer_group_id`
                            FROM `customer_group`
                            WHERE `customer_group_kid` = ".$kliet['idg']."
                            limit 1");
                            echo "<hr>";
            $customer->setData( 'group_id', $customer_group_id);
            $customer->save();
        }
    }
    public function reindex( $reindex ){
      foreach ($reindex as $value) {
        $process = Mage::getModel('index/process')->load( $value )->reindexAll();
      }
    }
    public function conf(){
      $xml=simplexml_load_file( Mage::getBaseDir().DS.'integrator'.DS."artykuly.xml" );
      $towary = $xml->art;
      print_r( $towary );
      echo "<pre>";
      foreach ($towary as $value)
      {
        if( (string)$value->han[0] == (string)$value->kat[0])
        {
          continue;
        }
        $tmp[ (string)$value->han[0] ]['simples_skus'][] = (string)$value->kat[0];
        $tmp[ (string)$value->han[0] ]['typ'] = (string)$value->p3[0];
        $tmp[ (string)$value->han[0] ]['price'] = (string)$value->cena_n[0];
        $tmp[ (string)$value->han[0] ]['name'] = 'pk --'.(string)$value->nazwa[0];

      }
      print_r( $tmp );
      foreach ($tmp as $key => $value)
      {
        require_once("lib/magmi/inc/magmi_defs.php");
        require_once("lib/magmi/integration/inc/magmi_datapump.php");
        $dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
        $dp->beginImportSession("Default","create");

        if( count( $value[ 'simples_skus' ] ) > 1 )
        {
            $value['sku'] = $key;
            $value['simples_skus'] = implode(',', $value[ 'simples_skus' ] );
            $value['type'] = 'configurable';
            $value['configurable_attributes'] = 'rozmiar';
            $value['tax_class_id'] = 5;

            if( Mage::getSingleton('catalog/product')->getIdBySku( $value['sku'] ) )
            {
              unset( $value['name'] );
            }
            print_r( $value );
            $dp->ingest( $value );
            $dp->endImportSession();
        }
      }
      echo "<hr>";
      print_r( $tmp );
    }
}
