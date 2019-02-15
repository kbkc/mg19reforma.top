<?php

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'mageneo_flipbook/book'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mageneo_flipbook/book'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Page Id')
    ->addColumn('route', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => false
    ), 'Route')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Active')
    ->addColumn('content_before', Varien_Db_Ddl_Table::TYPE_TEXT, '32k', array(
    ), 'Content Before Book')
    ->addColumn('content_after', Varien_Db_Ddl_Table::TYPE_TEXT, '32k', array(
    ), 'Content After Book')
    ->addColumn('user', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => false
    ), 'User-creator')
    ->addColumn('update_user', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => false
    ), 'User Update')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Created at')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Updated at')
    ->setComment('Flipbook Book Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'mageneo_flipbook/page'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mageneo_flipbook/page'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Page Id')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Active')
    ->addColumn('thumbnail_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false
    ), 'Thumbnail Image')
    ->addColumn('base_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false
    ), 'Base Image')
    ->addColumn('large_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false
    ), 'Large Image')
    ->addColumn('user', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => false
    ), 'User-creator')
    ->addColumn('update_user', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
        'nullable'  => false
    ), 'User Update')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Created at')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Updated at')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Sort Order')
    ->setComment('Flipbook Page Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'mageneo_flipbook/store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mageneo_flipbook/store'))
    ->addColumn('book_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Book Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Store Id')
    ->setComment('Flipbook Store Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'mageneo_flipbook/page_book'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mageneo_flipbook/page_book'))
    ->addColumn('book_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Book Id')
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Page Id')
    ->setComment('Flipbook Page-Book Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
