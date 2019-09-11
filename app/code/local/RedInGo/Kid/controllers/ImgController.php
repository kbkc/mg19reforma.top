<?php
class RedInGo_Kid_ImgController extends Mage_Core_Controller_Front_Action{

    public function IndexAction() {
      echo "<pre>";
      $dir   = Mage::getBaseDir().DS.'integrator'.DS.'zdjecia';
      $media = Mage::getBaseDir('media').DS.'catalog'.DS.'product';

      $resource = Mage::getSingleton('core/resource');
      $write = $resource->getConnection('core_write');
      $read  = $resource->getConnection('core_read');
      $table = $resource->getTableName('catalog_product_entity_media_gallery');

      $files = scandir($dir);
      $folders = array('..', '.');
      $files = array_diff( $files , $folders);

      foreach ($files as $value) {
        $sku = explode( '.', $value )[ 0 ]; //
        if( $id = Mage::getSingleton("catalog/product")->getIdBySku( $sku ) )
        {
          echo '<hr>';
          echo $sku;
          echo '<hr>';
          mkdir( $media.DS.$sku[0] );
          mkdir( $media.DS.$sku[0].DS.$sku[1] );
          $img = DS.$sku[0].DS.$sku[1].DS.$value;
          copy( $dir.DS.$value, $media.$img);

          $imp_att = [
            'media_gallery',
            'thumbnail',
           	'small_image',
            'image',
          ];
          $imp_att_id = [];

          foreach ($imp_att as $imp_att_value) {
            $imp_att_id[] = $read->fetchOne(
              "SELECT `attribute_id`
              FROM `eav_attribute`
              WHERE `attribute_code` = '$imp_att_value'
              limit 1"
            );
          }

          $write->insert(
              $table,
              [
                'attribute_id' => $imp_att_id[0],
                'entity_id' => $id,
                'value' => $img
              ],
              "`attribute_id` != $id AND `value` != '$img'"
          );

          unset( $imp_att_id[ 0 ] );
          foreach ($imp_att_id as $value) {
            $query =
                    "INSERT INTO `catalog_product_entity_varchar`
                    (`entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`)
                    VALUES ('4', '$value', '0', '$id', '$img')
                    ON DUPLICATE KEY UPDATE `value`= '$img'";
            $write->query( $query );
          }
        }
      }
      //print_r( $files1 );
    }
}
