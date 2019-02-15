<?php
$slidesCollection = Mage::getModel('magicslider/magicslider')->getCollection();
$search = ['<h3', 'h3>'];
$replace = ['<div', 'div>'];

foreach ($slidesCollection as $slide) {
    $slide->setContent(str_replace($search, $replace, $slide->getContent()))->save();
}
