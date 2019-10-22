<?php
class RedInGo_Kid_Block_Render_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
public function render(Varien_Object $row) {
    
    $id = $row->getData($this->getColumn()->getIndex());
    $model = Mage::getModel("kid/grupa")->load($id);
    $tmp = array();
    for ($i = 2; $i < 10; $i++) {
        if( $model->getData('p'.$i) ){
            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(4, Mage::getStoreConfig('kid/pole/p'.$i) );
            $tmp[] =  $attributeModel->getData('frontend_label') ;
        }
    };
    return implode(',', $tmp);
    

 
}
 
}
?>