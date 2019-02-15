<?php
$quickOrderItem = Mage::getModel('magicmenu/menu')->load('quickorder', 'link');
if (!$quickOrderItem->getId()) {
    $quickOrderItem->setData([
        'name' => 'Quick Order',
        'link' => 'quickorder',
        'stores' => null,
        'status' => '1',
        'extra' => 1,
    ]);
    $quickOrderItem->save();
}