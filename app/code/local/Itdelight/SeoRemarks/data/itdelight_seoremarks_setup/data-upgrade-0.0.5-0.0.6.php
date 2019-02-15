<?php
$pagesToDisable = Mage::getModel('cms/page')->getCollection()->addFilter('identifier', 'catalogue-reforma');
if (count($pagesToDisable)) {
    foreach ($pagesToDisable as $page) {
        $page->setIsActive('0')->setStores(array(0))->save();
    }
}

$extMenuDisable = Mage::getModel('magicmenu/menu')->getCollection()->addFilter('link', 'catalogue-reforma')->getFirstItem();
$extMenuDisable->setStatus('2')->setStores(null)->save();

$ruStoreId = Mage::getModel('core/store')->loadConfig('ru')->getId();


$contactPageRu = Mage::getModel('cms/page')->getCollection()->addStoreFilter($ruStoreId, false)->addFilter('identifier', 'contact')->getFirstItem();

$contactPageRu->setContent(<<<HTML
<div class="cms-page-contact contact" id="cms-page-contact">
    <div class="map-store">Уважаемые клиенты<br /> Оформить заказ и уточнить любые вопросы Вы можете по следующим телефонам:<br /> тел. <span class="bold">0 800 801 121</span> (бесплатно для всех операторов)
        <div class="feed-back"><form action="#" class="form" method="post">
            <div class="form-content row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="name-box box"><label class="required">Имя<em>*</em></label> <input class="input-text required-entry" name="name" title="Name" type="text" value="" /></div>
                    <div class="email-box box"><label class="required">Электронный адрес<em>*</em></label> <input class="input-text required-entry validate-email" name="email" title="Email" type="text" value="" /></div>
                    <div class="website-box box"><label>Сайт</label> <input class="input-text" name="website" title="website" type="text" value="" /></div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="comment-box box"><label class="required">Ваше сообщение:<em>*</em></label> <textarea class="required-entry input-text" name="comment" rows="10" title="Comment"></textarea></div>
                </div>
            </div>
            <button class="button"><span>Отправить</span></button></form></div>
    </div>
</div>
HTML
)->setStores(array($ruStoreId))->save();


$uaStoreId = Mage::getModel('core/store')->loadConfig('ua')->getId();
$contactPageUa = Mage::getModel('cms/page')->getCollection()->addStoreFilter($uaStoreId, false)->addFilter('identifier', 'contact')->getFirstItem();



$replaceHtmlUa = <<<HTML
<div class="map-store">Шановні клієнти<br /> Оформити замовлення та уточнити будь-які питання Ви можете за наступними телефонами:<br /> тел. <span class="bold">0 800 801 121</span> (безкоштовно для всіх операторів)
HTML;

$contactPageUa->setContent(<<<HTML
<div class="cms-page-contact contact" id="cms-page-contact">
    <div class="map-store">Шановні клієнти<br /> Оформити замовлення та уточнити будь-які питання Ви можете за наступними телефонами:<br /> тел. <span class="bold">0 800 801 121</span> (безкоштовно для всіх операторів)
        <div class="feed-back"><form action="#" class="form" method="post">
            <div class="form-content row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="name-box box"><label class="required">Имя<em>*</em></label> <input class="input-text required-entry" name="name" title="Name" type="text" value="" /></div>
                    <div class="email-box box"><label class="required">Электронный адрес<em>*</em></label> <input class="input-text required-entry validate-email" name="email" title="Email" type="text" value="" /></div>
                    <div class="website-box box"><label>Сайт</label> <input class="input-text" name="website" title="website" type="text" value="" /></div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="comment-box box"><label class="required">Ваше сообщение:<em>*</em></label> <textarea class="required-entry input-text" name="comment" rows="10" title="Comment"></textarea></div>
                </div>
            </div>
            <button class="button"><span>Отправить</span></button></form></div>
    </div>
</div>
HTML
)->setStores(array($uaStoreId))->save();


$albums = Mage::getModel('gallerymedia/galleryalbums')->getCollection()->addFilter('album_name', 'Nail Art');
if (count($albums)) {
    foreach ($albums as $album) {
        $album->setStatus('0')->save();
    }
}

$ruFooter = Mage::getModel('cms/block')->getCollection()->addStoreFilter($ruStoreId, false)->addFilter('identifier', 'cms-static-policy-footer')->getFirstItem();
$searchRu = ['<li><a href="{{store url=\'help\'}}">Поддержка</a></li>',
    '<li><a href="{{store url=\'return\'}}">Условия возврата</a></li>',
    '<li><a href="{{store url=\'affliates\'}}">Представительства</a></li>',
    '<li><a href="{{store url=\'legal\'}}">Юридическое предостережение</a></li>',
    '<li><a href="{{store url=\'terms\'}}">Условия использования</a></li>'];
$replaceRu = ['','','','','<li><a href="{{store url=\'contacts\'}}">Контакты</a></li>'];
$newFooterRu = str_replace($searchRu, $replaceRu, $ruFooter->getContent());
$ruFooter->setContent($newFooterRu)->setStores(array($ruStoreId))->save();

$uaFooter = Mage::getModel('cms/block')->getCollection()->addStoreFilter($uaStoreId, false)->addFilter('identifier', 'cms-static-policy-footer')->getFirstItem();
$searchUa = ['<li><a href="{{store url=\'help\'}}">Підтримка</a></li>',
    '<li><a href="{{store url=\'return\'}}">Умови повернення</a></li>',
    '<li><a href="{{store url=\'affliates\'}}">Представництва</a></li>',
    '<li><a href="{{store url=\'legal\'}}">Юридичне застереження</a></li>',
    '<li><a href="{{store url=\'terms\'}}">Умови користування</a></li>'];
$replaceUa = ['','','','','<li><a href="{{store url=\'contacts\'}}">Контакти</a></li>'];
$newFooterUa = str_replace($searchUa, $replaceUa, $uaFooter->getContent());
$uaFooter->setContent($newFooterUa)->setStores(array($uaStoreId))->save();








