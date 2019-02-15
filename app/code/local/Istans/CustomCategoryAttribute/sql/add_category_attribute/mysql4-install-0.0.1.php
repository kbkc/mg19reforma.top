<?php
$this->startSetup();
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'catalogue_display', array(
    'group'         => 'General Information',
    'input'         => 'select',
    'type'          => 'int',
    'source'        => 'eav/entity_attribute_source_boolean',
    'label'         => 'Display on Catalogue page',
    'backend'       => '',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'default'       => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'position'            => 100,
));
 
$this->endSetup();