<?php
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$address_line_2_origin_data = array (
    'attribute_set' =>  'Default',
    'group' => 'ShipHawk Attributes',
    'label'    => 'Origin Address 2',
    'visible'     => true,
    'type'     => 'varchar',
    'input'    => 'text',
    'system'   => false,
    'required' => false,
    'user_defined' => 1,
);

$installer->addAttribute('catalog_product','shiphawk_origin_addressline2',$address_line_2_origin_data);

$installer->endSetup();