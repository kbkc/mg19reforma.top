<?php
$installer = $this;
$connection = $installer->getConnection();
 
$installer->startSetup();
 
$installer->getConnection()
    ->addColumn($this->getTable('istans_nailgallery/gallery'),
    'order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Order'
    )
);
$installer->getConnection()
    ->addColumn($this->getTable('istans_nailgallery/event'),
    'order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Order'
    )
);
$installer->getConnection()
    ->addColumn($this->getTable('istans_nailgallery/video'),
    'order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Order'
    )
);
$installer->getConnection()
    ->addColumn($this->getTable('istans_nailgallery/eventimage'),
    'order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Order'
    )
);
 
$installer->endSetup();