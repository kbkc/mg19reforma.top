<?php 
/**
 * Istans_Nailgallery extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Istans
 * @package        Istans_Nailgallery
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Router
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * init routes
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Istans_Nailgallery_Controller_Router
     * @author Ultimate Module Creator
     */
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('istans_nailgallery', $this);
        return $this;
    }

    /**
     * Validate and match entities and modify request
     *
     * @access public
     * @param Zend_Controller_Request_Http $request
     * @return bool
     * @author Ultimate Module Creator
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $urlKey = trim($request->getPathInfo(), '/');
        $check = array();
        $check['gallery'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('istans_nailgallery/gallery/url_prefix'),
                'suffix'        => Mage::getStoreConfig('istans_nailgallery/gallery/url_suffix'),
                'list_key'      => Mage::getStoreConfig('istans_nailgallery/gallery/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'istans_nailgallery/gallery',
                'controller'    => 'gallery',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );
        $check['event'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('istans_nailgallery/event/url_prefix'),
                'suffix'        => Mage::getStoreConfig('istans_nailgallery/event/url_suffix'),
                'list_key'      => Mage::getStoreConfig('istans_nailgallery/event/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'istans_nailgallery/event',
                'controller'    => 'event',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );
        $check['video'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('istans_nailgallery/video/url_prefix'),
                'suffix'        => Mage::getStoreConfig('istans_nailgallery/video/url_suffix'),
                'list_key'      => Mage::getStoreConfig('istans_nailgallery/video/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'istans_nailgallery/video',
                'controller'    => 'video',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );
        $check['eventimage'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('istans_nailgallery/eventimage/url_prefix'),
                'suffix'        => Mage::getStoreConfig('istans_nailgallery/eventimage/url_suffix'),
                'list_key'      => Mage::getStoreConfig('istans_nailgallery/eventimage/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'istans_nailgallery/eventimage',
                'controller'    => 'eventimage',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );
        foreach ($check as $key=>$settings) {
            if ($settings->getListKey()) {
                if ($urlKey == $settings->getListKey()) {
                    $request->setModuleName('istans_nailgallery')
                        ->setControllerName($settings->getController())
                        ->setActionName($settings->getListAction());
                    $request->setAlias(
                        Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                        $urlKey
                    );
                    return true;
                }
            }
            if ($settings['prefix']) {
                $parts = explode('/', $urlKey);
                if ($parts[0] != $settings['prefix'] || count($parts) != 2) {
                    continue;
                }
                $urlKey = $parts[1];
            }
            if ($settings['suffix']) {
                $urlKey = substr($urlKey, 0, -strlen($settings['suffix']) - 1);
            }
            $model = Mage::getModel($settings->getModel());
            $id = $model->checkUrlKey($urlKey, Mage::app()->getStore()->getId());
            if ($id) {
                if ($settings->getCheckPath() && !$model->load($id)->getStatusPath()) {
                    continue;
                }
                $request->setModuleName('istans_nailgallery')
                    ->setControllerName($settings->getController())
                    ->setActionName($settings->getAction())
                    ->setParam($settings->getParam(), $id);
                $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $urlKey
                );
                return true;
            }
        }
        return false;
    }
}
