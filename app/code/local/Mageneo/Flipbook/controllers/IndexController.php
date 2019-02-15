<?php

class Mageneo_Flipbook_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $layout = $this->getLayout();
        /**
         * @var $headBlock Mage_Page_Block_Html_Head
         */
        $headBlock = $layout->getBlock('head');
        $headBlock->addJs('lib/jquery/noconflict.js')
            ->addJs('catalogue/extras/jquery-migrate-1.2.1.js')
            ->addJs('catalogue/extras/jquery-ui-1.8.20.custom.min.js')
            ->addJs('catalogue/extras/modernizr.2.5.3.min.js')
            ->addJs('catalogue/lib/hash.js');

        $this->renderLayout();
    }
}