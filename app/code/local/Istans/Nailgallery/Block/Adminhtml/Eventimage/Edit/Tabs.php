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
 * Event image admin edit tabs
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Adminhtml_Eventimage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('eventimage_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('istans_nailgallery')->__('Event image'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Eventimage_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_eventimage',
            array(
                'label'   => Mage::helper('istans_nailgallery')->__('Event image'),
                'title'   => Mage::helper('istans_nailgallery')->__('Event image'),
                'content' => $this->getLayout()->createBlock(
                    'istans_nailgallery/adminhtml_eventimage_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'form_meta_eventimage',
            array(
                'label'   => Mage::helper('istans_nailgallery')->__('Meta'),
                'title'   => Mage::helper('istans_nailgallery')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'istans_nailgallery/adminhtml_eventimage_edit_tab_meta'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_eventimage',
                array(
                    'label'   => Mage::helper('istans_nailgallery')->__('Store views'),
                    'title'   => Mage::helper('istans_nailgallery')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'istans_nailgallery/adminhtml_eventimage_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve event image entity
     *
     * @access public
     * @return Istans_Nailgallery_Model_Eventimage
     * @author Ultimate Module Creator
     */
    public function getEventimage()
    {
        return Mage::registry('current_eventimage');
    }
}
