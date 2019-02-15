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
class Zitec_Dpd_Model_Payment_Cashondelivery_Source_Service
{
    protected $_allowedServices = null;

    /**
     *
     * @param boolean $isMultiselect
     *
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $servicesSource = Mage::getModel('zitec_dpd/config_source_service');
        $options = array();
        foreach ($servicesSource->getAvailableServices() as $serviceCode => $label) {
            if (in_array($serviceCode, $this->_getAllowedServices())) {
                $options[] = array('label' => $serviceCode . ' - ' . $label, 'value' => $serviceCode);
            }
        }

        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }

    /**
     *
     * @return array
     */
    protected function _getAllowedServices()
    {
        if (!is_array($this->_allowedServices)) {
            $this->_allowedServices = explode(",", Mage::getStoreConfig("payment/zitec_dpd_cashondelivery/services"));
        }

        return $this->_allowedServices;
    }


}



