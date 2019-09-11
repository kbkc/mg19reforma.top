<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "wfmag",  array(
    "type"     => "text",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Kategorie Wf-mag",
    "input"    => "select",
    "class"    => "",
    "source"   => "kid/eav_entity_attribute_source_categoryoptions13974527110",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "Mapowanie kategori",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

	));
$installer->endSetup();
	 