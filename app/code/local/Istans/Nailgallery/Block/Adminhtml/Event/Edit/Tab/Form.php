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
 * Event edit form tab
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Adminhtml_Event_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Event_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('event_');
        $form->setFieldNameSuffix('event');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'event_form',
            array('legend' => Mage::helper('istans_nailgallery')->__('Event'))
        );
        $fieldset->addType(
            'file',
            Mage::getConfig()->getBlockClassName('istans_nailgallery/adminhtml_event_helper_file')
        );

        $fieldset->addField(
            'image',
            'file',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Image'),
                'name'  => 'image',

           )
        );

        $fieldset->addField(
            'caption',
            'text',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Caption'),
                'name'  => 'caption',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'order',
            'text',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Order'),
                'name'  => 'order',
            )
        );
        $fieldset->addField(
            'url_key',
            'text',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Url key'),
                'name'  => 'url_key',
                'note'  => Mage::helper('istans_nailgallery')->__('Relative to Website Base URL')
            )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('istans_nailgallery')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('istans_nailgallery')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('istans_nailgallery')->__('Disabled'),
                    ),
                ),
            )
        );
        $fieldset->addField(
            'in_rss',
            'select',
            array(
                'label'  => Mage::helper('istans_nailgallery')->__('Show in rss'),
                'name'   => 'in_rss',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('istans_nailgallery')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('istans_nailgallery')->__('No'),
                    ),
                ),
            )
        );
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_event')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_event')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getEventData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getEventData());
            Mage::getSingleton('adminhtml/session')->setEventData(null);
        } elseif (Mage::registry('current_event')) {
            $formValues = array_merge($formValues, Mage::registry('current_event')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
