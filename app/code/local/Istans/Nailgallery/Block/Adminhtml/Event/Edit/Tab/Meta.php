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
 * meta information tab
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Adminhtml_Event_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Event_Edit_Tab_Meta
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('event');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'event_meta_form',
            array('legend' => Mage::helper('istans_nailgallery')->__('Meta information'))
        );
        $fieldset->addField(
            'meta_title',
            'text',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Meta-title'),
                'name'  => 'meta_title',
            )
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            array(
                'name'      => 'meta_description',
                'label'     => Mage::helper('istans_nailgallery')->__('Meta-description'),
              )
        );
        $fieldset->addField(
            'meta_keywords',
            'textarea',
            array(
                'name'      => 'meta_keywords',
                'label'     => Mage::helper('istans_nailgallery')->__('Meta-keywords'),
            )
        );
        $form->addValues(Mage::registry('current_event')->getData());
        return parent::_prepareForm();
    }
}
