<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
$ruStoreId = Mage::getModel('core/store')->load('ru', 'code')->getId();
$uaStoreId = Mage::getModel('core/store')->load('ua', 'code')->getId();
$installer->startSetup();

$paymentPageRu = Mage::getModel('cms/page')->getCollection()
    ->addStoreFilter($ruStoreId, false)
    ->addFilter('identifier', 'payment')
    ->getFirstItem();

$paymentPageDataRu = array(
    'title' => 'Оплата',
    'root_template' => 'one_column',
    'identifier' => 'payment',
    'is_active' => '1',
    'stores' => array($ruStoreId),//available for all store views
    'content' => ''
);
if (!$paymentPageRu->getId()) {
    Mage::getModel('cms/page')->setData($paymentPageDataRu)->save();
}

$paymentPageUa = Mage::getModel('cms/page')->getCollection()
    ->addStoreFilter($uaStoreId, false)
    ->addFilter('identifier', 'payment')
    ->getFirstItem();

if (!$paymentPageUa->getId()) {
    $paymentPageDataUa = $paymentPageDataRu;
    $paymentPageDataUa['stores'] = array($uaStoreId);

    Mage::getModel('cms/page')->setData($paymentPageDataUa)->save();
}

$ruFooter = Mage::getModel('cms/block')->getCollection()->addStoreFilter($ruStoreId, false)
    ->addFilter('identifier', 'cms-static-policy-footer')
    ->getFirstItem();
$paymentStr = '<li><a href="{{store url=\'payment\'}}">Оплата</a></li>';
if ($ruFooter->getId() && strpos($ruFooter->getContent(), $paymentStr) === false) {
    $ruFooter->setContent(<<<HTML
<div class="cms-static-policy-footer policy-footer" id="cms-static-policy-footer">
<ul class="policy-footer-link">
<li><a href="{{store url='payment'}}">Оплата</a></li>
<li><a href="{{store url='shipping'}}">Доставка</a></li>
<li><a href="{{store url='contacts'}}">Контакты</a></li>
</ul>
</div>
HTML
)->setStores(array($ruStoreId))->save();
}

$uaFooter = Mage::getModel('cms/block')->getCollection()->addStoreFilter($uaStoreId, false)
    ->addFilter('identifier', 'cms-static-policy-footer')
    ->getFirstItem();
if ($uaFooter->getId() && strpos($uaFooter->getContent(), $paymentStr) === false) {
    $uaFooter->setContent(<<<HTML
<div class="cms-static-policy-footer policy-footer" id="cms-static-policy-footer">
<ul class="policy-footer-link">
<li><a href="{{store url='payment'}}">Оплата</a></li>
<li><a href="{{store url='shipping'}}">Доставка</a></li>
<li><a href="{{store url='contacts'}}">Контакти</a></li>
</ul>
</div>
HTML
)->setStores(array($uaStoreId))->save();
}

$installer->endSetup();

