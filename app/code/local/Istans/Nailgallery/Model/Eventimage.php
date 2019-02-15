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
 * Event image model
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Model_Eventimage extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'istans_nailgallery_eventimage';
    const CACHE_TAG = 'istans_nailgallery_eventimage';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'istans_nailgallery_eventimage';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'eventimage';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('istans_nailgallery/eventimage');
    }

    /**
     * before save event image
     *
     * @access protected
     * @return Istans_Nailgallery_Model_Eventimage
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the event image details page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getEventimageUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('istans_nailgallery/eventimage/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('istans_nailgallery/eventimage/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('istans_nailgallery/eventimage/view', array('id'=>$this->getId()));
    }

    /**
     * check URL key
     *
     * @access public
     * @param string $urlKey
     * @param bool $active
     * @return mixed
     * @author Ultimate Module Creator
     */
    public function checkUrlKey($urlKey, $active = true)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    /**
     * save event image relation
     *
     * @access public
     * @return Istans_Nailgallery_Model_Eventimage
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve parent 
     *
     * @access public
     * @return null|Istans_Nailgallery_Model_Event
     * @author Ultimate Module Creator
     */
    public function getParentEvent()
    {
        if (!$this->hasData('_parent_event')) {
            if (!$this->getEventId()) {
                return null;
            } else {
                $event = Mage::getModel('istans_nailgallery/event')
                    ->load($this->getEventId());
                if ($event->getId()) {
                    $this->setData('_parent_event', $event);
                } else {
                    $this->setData('_parent_event', null);
                }
            }
        }
        return $this->getData('_parent_event');
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        return $values;
    }
    
}
