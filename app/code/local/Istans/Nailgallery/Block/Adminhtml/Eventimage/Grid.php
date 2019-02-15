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
 * Event image admin grid block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Adminhtml_Eventimage_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('eventimageGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Eventimage_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('istans_nailgallery/eventimage')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Eventimage_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('istans_nailgallery')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'event_id',
            array(
                'header'    => Mage::helper('istans_nailgallery')->__('Event'),
                'index'     => 'event_id',
                'type'      => 'options',
                'options'   => Mage::getResourceModel('istans_nailgallery/event_collection')
                    ->toOptionHash(),
                'renderer'  => 'istans_nailgallery/adminhtml_helper_column_renderer_parent',
                'params'    => array(
                    'id'    => 'getEventId'
                ),
                'base_link' => 'adminhtml/nailgallery_event/edit'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('istans_nailgallery')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('istans_nailgallery')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('istans_nailgallery')->__('Enabled'),
                    '0' => Mage::helper('istans_nailgallery')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'file',
            array(
                'header' => Mage::helper('istans_nailgallery')->__('File'),
                'index'  => 'file',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'url_key',
            array(
                'header' => Mage::helper('istans_nailgallery')->__('URL key'),
                'index'  => 'url_key',
            )
        );
        $this->addColumn(
            'order',
            array(
                'header' => Mage::helper('istans_nailgallery')->__('Order key'),
                'index'  => 'order',
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('istans_nailgallery')->__('Store Views'),
                    'index'      => 'store_id',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback'=> array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('istans_nailgallery')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('istans_nailgallery')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('istans_nailgallery')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('istans_nailgallery')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('istans_nailgallery')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('istans_nailgallery')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('istans_nailgallery')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Eventimage_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('eventimage');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('istans_nailgallery')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('istans_nailgallery')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('istans_nailgallery')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('istans_nailgallery')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('istans_nailgallery')->__('Enabled'),
                            '0' => Mage::helper('istans_nailgallery')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $values = Mage::getResourceModel('istans_nailgallery/event_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem(
            'event_id',
            array(
                'label'      => Mage::helper('istans_nailgallery')->__('Change Event'),
                'url'        => $this->getUrl('*/*/massEventId', array('_current'=>true)),
                'additional' => array(
                    'flag_event_id' => array(
                        'name'   => 'flag_event_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('istans_nailgallery')->__('Event'),
                        'values' => $values
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Istans_Nailgallery_Model_Eventimage
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Adminhtml_Eventimage_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * filter store column
     *
     * @access protected
     * @param Istans_Nailgallery_Model_Resource_Eventimage_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Istans_Nailgallery_Block_Adminhtml_Eventimage_Grid
     * @author Ultimate Module Creator
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
