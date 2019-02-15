<?php
foreach (Mage::getModel('blog/blog')->getCollection()->getItems() as $post) {
    $content = $post->getPostContent();
    $content =preg_replace('#<a href="pro.*>#USi', '', $content);
    $content =preg_replace('#</a>#USi', '', $content);
    $post->setPostContent($content)->save();
}


