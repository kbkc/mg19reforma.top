<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("quote_address", "discount_total", array("type"=>"varchar"));
$installer->addAttribute("order", "discount_total", array("type"=>"varchar"));
$installer->endSetup();
	 