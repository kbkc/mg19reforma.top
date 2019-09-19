<?php


$installer = $this;

$installer->startSetup();

$installer->addAttribute("customer", "what_you_do",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "What you do",
    "input"    => "text",
    "source"   => "Registration_Poll_Model_Attribute_Source_Module",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => "What you do"
));

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('registrationFormPoll')};");
$table = $installer->getConnection()->newTable($installer->getTable('registrationFormPoll'))
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true,
        ), 'User ID')
    ->addColumn('question', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        'default' => '', 
        ), 'Question')
    ->addColumn('answer', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        'default' => '', 
        ), 'Answer')
    ->setComment('Poll table');
    $installer->getConnection()->createTable($table);
    
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'what_you_do');
$attribute->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create','customer_account_edit'));
$attribute->setData('is_user_defined', 0);
$attribute->save();
$installer->endSetup();