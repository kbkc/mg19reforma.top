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
 * Event image edit form tab
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Adminhtml_Eventimage_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Eventimage_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('eventimage_');
        $form->setFieldNameSuffix('eventimage');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'eventimage_form',
            array('legend' => Mage::helper('istans_nailgallery')->__('Event image'))
        );
        $values = Mage::getResourceModel('istans_nailgallery/event_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="eventimage_event_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeEventIdLink() {
                if ($(\'eventimage_event_id\').value == \'\') {
                    $(\'eventimage_event_id_link\').hide();
                } else {
                    $(\'eventimage_event_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/nailgallery_event/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'eventimage_event_id\').value);
                    $(\'eventimage_event_id_link\').href = realUrl;
                    $(\'eventimage_event_id_link\').innerHTML = text.replace(\'{#name}\', $(\'eventimage_event_id\').options[$(\'eventimage_event_id\').selectedIndex].innerHTML);
                }
            }
            $(\'eventimage_event_id\').observe(\'change\', changeEventIdLink);
            changeEventIdLink();
            </script>';

        $fieldset->addField(
            'event_id',
            'select',
            array(
                'label'     => Mage::helper('istans_nailgallery')->__('Event'),
                'name'      => 'event_id',
                'required'  => false,
                'values'    => $values,
                'after_element_html' => $html
            )
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
        
        $fieldset->addType(
            'image',
            Mage::getConfig()->getBlockClassName('istans_nailgallery/adminhtml_eventimage_helper_image')
        );

        $fieldset->addField(
            'file',
            'image',
            array(
                'label' => Mage::helper('istans_nailgallery')->__('Image'),
                'name'  => 'file',
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
            Mage::registry('current_eventimage')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_eventimage')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getEventimageData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getEventimageData());
            Mage::getSingleton('adminhtml/session')->setEventimageData(null);
        } elseif (Mage::registry('current_eventimage')) {
            $formValues = array_merge($formValues, Mage::registry('current_eventimage')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
