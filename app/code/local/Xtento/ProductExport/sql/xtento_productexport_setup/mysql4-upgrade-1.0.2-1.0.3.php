<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `" . $this->getTable('xtento_productexport_profile') . "`
ADD `start_download_manual_export` INT(1) NOT NULL DEFAULT '1' AFTER `save_files_manual_export`;
");

$installer->endSetup();