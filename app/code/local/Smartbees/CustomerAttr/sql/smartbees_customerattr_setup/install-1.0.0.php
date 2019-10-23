<?php


$installer = $this;

$installer->startSetup();

$installer->addAttribute("customer", "agree_messages",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Agree Messages",
    "input"    => "text",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => "Agree Messages"
));
    
$installer->addAttribute("customer", "agree_rodo",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Agree Rodo",
    "input"    => "text",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => "Agree Rodo"
));
    
$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'agree_messages');
$attribute1 = Mage::getSingleton('eav/config')->getAttribute('customer', 'agree_rodo');
$attribute->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create','customer_account_edit'));
$attribute1->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create','customer_account_edit'));
$attribute->setData('is_user_defined', 0);
$attribute1->setData('is_user_defined', 0);
$attribute->save();
$attribute1->save();
$installer->endSetup();