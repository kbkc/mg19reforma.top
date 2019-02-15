<?php
/**
 * Zitec_Dpd – shipping carrier extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Zitec
 * @package    Zitec_Dpd
 * @copyright  Copyright (c) 2014 Zitec COM
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @category   Zitec
 * @package    Zitec_Dpd
 * @author     Zitec COM <magento@zitec.ro>
 */
class Zitec_PrintShippingLabel_Block_Adminhtml_Sales_Order_Shipment_View extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{

    public function __construct()
    {
        parent::__construct();
        if ($this->_hasPdfShippingLabel()) {
            $this->_addButton('zitec_print_shipping_label', array(
                'label'   => $this->_getHelper()->__('Print Shipping Labels'),
                'onclick' => "setLocation('{$this->_getPrintShippingLabelsUrl()}')"
            ));
        }
    }

    /**
     * @return bool
     */
    protected function _hasPdfShippingLabel()
    {
        return $this->getShipment()->getShippingLabel() ? true : false;
    }

    /**
     *
     * @return string
     */
    protected function _getPrintShippingLabelsUrl()
    {
        return Mage::helper('adminhtml')->getUrl('zitec_printshippinglabel/adminhtml_index/printshippinglabels', array('shipment_id' => $this->getShipment()->getId()));
    }

    /**
     *
     * @return Zitec_PrintShippingLabel_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('zitec_printshippinglabel');
    }

}