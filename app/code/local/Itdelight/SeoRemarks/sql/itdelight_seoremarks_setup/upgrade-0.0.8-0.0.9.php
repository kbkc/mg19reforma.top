<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
$ruStoreId = Mage::getModel('core/store')->load('ru', 'code')->getId();
$uaStoreId = Mage::getModel('core/store')->load('ua', 'code')->getId();
$identifier = 'privacy-policy';
$installer->startSetup();

$privacyPageRu = Mage::getModel('cms/page')->getCollection()
    ->addStoreFilter($ruStoreId, false)
    ->addFilter('identifier', 'privacy-policy')
    ->getFirstItem();

$privacyPageDataRu = array(
    'title' => 'Политика конфиденциальности',
    'root_template' => 'one_column',
    'identifier' => 'privacy-policy',
    'is_active' => '1',
    'stores' => array($ruStoreId),//available for all store views
    'content' => ''
);
if (!$privacyPageRu->getId()) {
    Mage::getModel('cms/page')->setData($privacyPageDataRu)->save();
}

$privacyPageUa = Mage::getModel('cms/page')->getCollection()
    ->addStoreFilter($uaStoreId, false)
    ->addFilter('identifier', 'privacy-policy')
    ->getFirstItem();

if (!$privacyPageUa->getId()) {
    $privacyPageDataUa = $privacyPageDataRu;
    $privacyPageDataUa['title'] = 'Політика конфіденційності';
    $privacyPageDataUa['stores'] = array($uaStoreId);

    Mage::getModel('cms/page')->setData($privacyPageDataUa)->save();
}

$ruFooter = Mage::getModel('cms/block')->getCollection()->addStoreFilter($ruStoreId, false)
    ->addFilter('identifier', 'cms-static-policy-footer')
    ->getFirstItem();
$privacyStr = '<li><a href="{{store url=\'privacy-policy\'}}">Политика конфиденциальности</a></li>';
if ($ruFooter->getId() && strpos($ruFooter->getContent(), $privacyStr) === false) {
    $ruFooter->setContent(<<<HTML
<div class="cms-static-policy-footer policy-footer" id="cms-static-policy-footer">
<ul class="policy-footer-link">
<li><a href="{{store url='privacy-policy'}}">Оплата</a></li>
<li><a href="{{store url='shipping'}}">Доставка</a></li>
<li><a href="{{store url='privacy-policy'}}">Политика конфиденциальности</a></li>
<li><a href="{{store url='contacts'}}">Контакты</a></li>
</ul>
</div>
HTML
    )->setStores(array($ruStoreId))->save();
}

$uaFooter = Mage::getModel('cms/block')->getCollection()->addStoreFilter($uaStoreId, false)
    ->addFilter('identifier', 'cms-static-policy-footer')
    ->getFirstItem();
$privacyStrUa = '<li><a href="{{store url=\'privacy-policy\'}}">Політика конфіденційності</a></li>';
if ($uaFooter->getId() && strpos($uaFooter->getContent(), $privacyStr) === false) {
    $uaFooter->setContent(<<<HTML
<div class="cms-static-policy-footer policy-footer" id="cms-static-policy-footer">
<ul class="policy-footer-link">
<li><a href="{{store url='privacy-policy'}}">Оплата</a></li>
<li><a href="{{store url='shipping'}}">Доставка</a></li>
<li><a href="{{store url='privacy-policy'}}">Політика конфіденційності</a></li>
<li><a href="{{store url='contacts'}}">Контакти</a></li>
</ul>
</div>
HTML
    )->setStores(array($uaStoreId))->save();
}

$installer->endSetup();

