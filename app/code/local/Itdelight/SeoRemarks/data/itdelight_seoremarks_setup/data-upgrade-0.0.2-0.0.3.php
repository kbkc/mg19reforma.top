<?php
$homePagePosts = Mage::getModel('cms/block')->getCollection()
    ->addFieldToFilter('identifier', 'cms-static-main-collection2');
$search = ['<h3', 'h3>'];
$replace = ['<p', 'p>'];

foreach ($homePagePosts as $post) {
    $storeIds = $post->getResource()->lookupStoreIds($post->getBlockId());
    $post->setContent(str_replace($search, $replace, $post->getContent()))
        ->setStores($storeIds)
        ->save();
}
