<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn(
    $installer->getTable('sales/quote_item'), 'gift_custom_price', "decimal(12,4) NOT NULL default '0.0000'"
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_item'), 'gift_custom_price', "decimal(12,4) NOT NULL default '0.0000'"
);
$installer->getConnection()->addColumn(
    $installer->getTable('salesrule'), 'gift_custom_price', "decimal(12,4) NOT NULL default '0.0000'"
);
$installer->endSetup();