<?php
/**
 * @copyright Copyright (c) 2014 Sergey Cherepanov (sergey@cherepanov.org.ua)
 * @license Creative Commons Attribution 3.0 License
 */

class Open_Gallery_Block_Adminhtml_Category_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $editUrl   = $this->getUrl('*/*/edit', array('id' => $row->getId()));
        $manageUrl = $this->getUrl('*/gallery_item/list', array('id' => $row->getId()));

        return '<a href="'.$editUrl.'">'.$this->__('Edit Category').'</a> <span>|</span> <a href="'.$manageUrl.'">'.$this->__('Manage Items').'</a> ';
    }
}
