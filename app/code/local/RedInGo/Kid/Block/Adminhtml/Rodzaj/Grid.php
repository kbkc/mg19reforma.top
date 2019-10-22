<?php

class RedInGo_Kid_Block_Adminhtml_Rodzaj_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId("rodzajGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection() {
        $collection = Mage::getModel("kid/rodzaj")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn("id", array(
            "header" => Mage::helper("kid")->__("ID"),
            "align" =>"right",
            "width" => "50px",
            "type" => "number",
            "index" => "id",
        ));
        $this->addColumn("nazwa", array(
        "header" => Mage::helper("kid")->__("nazwa"),
        "index" => "nazwa",
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }
    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('rodzaj', array(
            'label'=> Mage::helper('kid')->__('Rodzaj'),
            'url'  => $this->getUrl('*/adminhtml_rodzaj/massRodzaj'),
            'confirm' => Mage::helper('kid')->__('Are you sure?')
        ));
        return $this;
    }
}