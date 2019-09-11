<?php
class RedInGo_Kid_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
         
        $this->getMassactionBlock()->addItem(
            'zamowienie',
            array('label' => $this->__('Ponów synchronizację'),
                  'url'   => $this->getUrl('kid') //this should be the url where there will be mass operation
            )
        );
    }
    protected function _prepareColumns()
    {

        parent::_prepareColumns();
	
//        $this->addColumn('kid', array(
//            'header'    =>  'CRM',
//            'width'     =>  '100',
//            'renderer'  =>  'RedInGo_Kid_Block_Renderer',
//            'index'     =>  'kid',
//            'type' => 'options',
//            'options'=>RedInGo_Kid_Block_Adminhtml_Sales_Order_Grid::getOptionArray0(),
//
//        ));        
		
//        $this->addColumnsOrder('kid');


    }
	static public function getOptionArray0() {
		$data_array=array(); 
		$data_array[0]='Nowe';
		$data_array[1]='Wysłane';
                $data_array[2]='Odebrano';
		return($data_array);
	}
	
}
			
