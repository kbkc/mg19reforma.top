<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$baseTableName = 'p24_recuring';

$sql = "
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `{$this->getTable($baseTableName)}`
-- ----------------------------
DROP TABLE IF EXISTS {$this->getTable($baseTableName)};
CREATE TABLE IF NOT EXISTS {$this->getTable($baseTableName)} (
  `id` INT NOT NULL AUTO_INCREMENT,
  `customer` INT NOT NULL,
  `reference` varchar(64) NOT NULL,
  `expires` varchar(4) NOT NULL,
  `mask` varchar(32) NOT NULL,
  `card_type` varchar(255) NOT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_FIELDS` (`mask`,`card_type`,`expires`,`customer`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
";

$installer->run($sql);

$baseTableName = 'p24_lastmethod';

$sql = "
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `{$this->getTable($baseTableName)}`
-- ----------------------------
DROP TABLE IF EXISTS {$this->getTable($baseTableName)};
CREATE TABLE IF NOT EXISTS {$this->getTable($baseTableName)} (
  `customer` INT NOT NULL,
  `method` INT,
  PRIMARY KEY (`customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$installer->run($sql);

$installer->endSetup();