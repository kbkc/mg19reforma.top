<?php
$installer = $this;
$installer->startSetup();

$sql=<<<SQLTEXT
 ALTER TABLE `{$installer->getTable('redingo_kid')}` ADD `data` DATE NOT NULL 	;
 ALTER TABLE `{$installer->getTable('redingo_kid')}` ADD `date_create` DATE NOT NULL ;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
