<?php


$installer = $this;

$installer->startSetup();

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('ship_tracking_number_with_flag')};");
$table = $installer->getConnection()->newTable($installer->getTable('ship_tracking_number_with_flag'))
    ->addColumn( 'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID' )
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        'default' => '', 
        ), 'Order ID')
    ->addColumn('tracking_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        'default' => '', 
        ), 'Tracking number')
    ->addColumn('flag', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        'default' => '0', 
        ), 'Flaga')
    ->setComment('Numery przesyłek');

$installer->getConnection()->createTable($table);
    

$installer->endSetup();