<?php
class RedInGo_Kid_Block_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function render(Varien_Object $row) {
        $value =  $row->getData($this->getColumn()->getIndex());
        switch($value){
        case 1:
            return '<span style="color:green;">WYSŁANE</span>';
        case 2:
            return '<span style="color:#0d0;">ODEBRANO</span>';
        case 0: 
            return '<span style="color:blue;">NOWE</span>';
        }
    }
}
?>