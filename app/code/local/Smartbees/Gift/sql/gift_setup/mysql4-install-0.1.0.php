<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote_item'), 'is_gift', "tinyint(4) NOT NULL default '0'"
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_item'), 'is_gift', "tinyint(4) NOT NULL default '0'"
);
$installer->getConnection()->addColumn(
    $installer->getTable('salesrule'), 'gift_sku', "varchar(255) NOT NULL default ''"
);
$installer->endSetup();