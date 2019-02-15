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
 * Video admin edit form
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Adminhtml_Video_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'istans_nailgallery';
        $this->_controller = 'adminhtml_video';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('istans_nailgallery')->__('Save Video')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('istans_nailgallery')->__('Delete Video')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('istans_nailgallery')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_video') && Mage::registry('current_video')->getId()) {
            return Mage::helper('istans_nailgallery')->__(
                "Edit Video '%s'",
                $this->escapeHtml(Mage::registry('current_video')->getTitle())
            );
        } else {
            return Mage::helper('istans_nailgallery')->__('Add Video');
        }
    }
}
