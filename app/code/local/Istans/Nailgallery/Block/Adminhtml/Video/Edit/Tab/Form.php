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
 * Video edit form tab
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Adminhtml_Video_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Video_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('video_');
        $form->setFieldNameSuffix('video');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'video_form',
            array('legend' => Mage::helper('istans_nailgallery')->__('Video'))
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Title'),
                'name'  => 'title',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'video',
            'text',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Video ID'),
                'name'  => 'video',
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
            Mage::registry('current_video')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_video')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getVideoData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getVideoData());
            Mage::getSingleton('adminhtml/session')->setVideoData(null);
        } elseif (Mage::registry('current_video')) {
            $formValues = array_merge($formValues, Mage::registry('current_video')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
