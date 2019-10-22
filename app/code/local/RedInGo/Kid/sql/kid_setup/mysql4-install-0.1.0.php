<?php
$installer = $this;
$installer->startSetup();

$sql=<<<SQLTEXT
        
CREATE TABLE IF NOT EXISTS `{$installer->getTable('redingo_kategorie')}` (
  `kategoria_id` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(255) NOT NULL,
  `wfmag` int(11) NOT NULL,
  PRIMARY KEY (`kategoria_id`),
  UNIQUE KEY `wfmag` (`wfmag`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=0 ;
        
CREATE TABLE IF NOT EXISTS `{$installer->getTable('redingo_kid')}` (
  `kid_id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) NOT NULL,
  `wfmag` varchar(255) NOT NULL,
  PRIMARY KEY (`kid_id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin2 AUTO_INCREMENT=0 ;

 ALTER TABLE `{$installer->getTable('sales_flat_order')}` ADD `kid` INT( 11 ) NOT NULL 	;
 ALTER TABLE `{$installer->getTable('sales_flat_order_grid')}` ADD `kid` INT( 11 ) NOT NULL ;
 ALTER TABLE `{$installer->getTable('customer_group')}` ADD `customer_group_kid` INT( 11 ) NOT NULL ;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 