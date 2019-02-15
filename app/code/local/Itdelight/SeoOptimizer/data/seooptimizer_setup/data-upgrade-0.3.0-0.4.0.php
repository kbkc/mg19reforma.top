<?php
$r = 3;
$collection = Mage::getModel('catalog/category')
    ->getCollection();
foreach ($collection as $categoryId) {
    $category = Mage::getModel('catalog/category')->load($categoryId->getId());
    $content = $category->getDescription();
    if($content){
        $content =preg_replace('#<a.*>#USi', '', $content);
        $content =preg_replace('#</a>#USi', '', $content);
        $content =preg_replace('#<img.*>#USi', '', $content);
        $category->setDescription($content)->save();
    }
}
