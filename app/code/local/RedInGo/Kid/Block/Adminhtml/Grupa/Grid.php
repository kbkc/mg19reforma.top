<?php

class RedInGo_Kid_Block_Adminhtml_Grupa_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId("grupaGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("ASC");
      //  $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("kid/grupa")->getCollection();
 //       $collection->setOrder('id', 'desc');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn("id", array(
        "header" => Mage::helper("kid")->__("ID"),
        "align" =>"right",
        "width" => "50px",
        "type" => "number",
        "index" => "id",
        ));

        /*
        $this->addColumn("nazwa", array(
        "header" => Mage::helper("kid")->__("nazwa"),
        "index" => "nazwa",
        ));
         * 
         */
        
        $this->addColumn("gropa", array(
        "header" => Mage::helper("kid")->__("grupa"),
        "index" => "gropa",
        ));
        
//         $this->addColumn("pola", array(
//         "header" => Mage::helper("kid")->__("Ilość"),
//         'renderer'  =>  'RedInGo_Kid_Block_Render_Renderer',
//         "index" => "id",
//         ));
        
        $this->addColumn("type", array(
        "header" => Mage::helper("kid")->__("Typ"),
        "index" => "type",
        ));
        
        $this->addColumn("websites", array(
        "header" => Mage::helper("kid")->__("Websites"),
        "index" => "websites",
        ));
        $this->addColumn("pola", array(
        "header" => Mage::helper("kid")->__("Zgrupowane"),
        'renderer'  =>  'RedInGo_Kid_Block_Render_Renderer',
        "index" => "id",
        ));

        $this->addColumn("conf", array(
        "header" => Mage::helper("kid")->__("Ustawienia dodatkowe"),
        'renderer'  =>  'RedInGo_Kid_Block_Conf_Renderer',
        "index" => "id",
        ));
        
        
// $this->addColumn('action',
//         array(
//             'header'    => Mage::helper('catalog')->__('Action'),
//             'width'     => '50px',
//             'type'      => 'action',
//             'getter'     => 'getId',
//             'actions'   => array(
//                 array(
//                     'caption' => Mage::helper('kid')->__('Produkty'),
//                     'url'     => array(
//                         'base'=>'*/*/produkty',
//                         'params'=>array('store'=>$this->getRequest()->getParam('store'))
//                     ),
//                     'field'   => 'id'
//                 )
//             ),
//             'filter'    => false,
//             'sortable'  => false,
//             'index'     => 'stores',
//     ));
        
      //  $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
     //  $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_grupa', array(
            'label'=> Mage::helper('kid')->__('Remove Grupa'),
            'url'  => $this->getUrl('*/adminhtml_grupa/massRemove'),
            'confirm' => Mage::helper('kid')->__('Are you sure?')
        ));
        
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('product_grupa', array(
            'label'=> Mage::helper('kid')->__('Grupa'),
            'url'  => $this->getUrl('*/adminhtml_grupa/massProduct'),
           // 'confirm' => Mage::helper('kid')->__('Are you sure?')
        ));
        return $this;
    }
}