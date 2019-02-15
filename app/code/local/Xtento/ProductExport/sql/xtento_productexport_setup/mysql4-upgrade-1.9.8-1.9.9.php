<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE  `" . $this->getTable('xtento_productexport_profile_history') . "` ADD INDEX `ENTITY_ID_IDX` (`entity_id`);
");

$installer->endSetup();