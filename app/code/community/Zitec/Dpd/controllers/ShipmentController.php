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
class Zitec_Dpd_ShipmentController extends Mage_Core_Controller_Front_Action
{


    /**
     * this action is used to validate manually the address postcode
     */
    public function validatePostcodeAction(){
        $params = $this->getRequest()->getParams();
        if(!isset($params['street'])) {
            if(array_key_exists('billing', $params)) {
                $params = $params['billing'];
            } else if(array_key_exists('shipping', $params)) {
                $params = $params['shipping'];
            }
        }
        if($this->validAddress($params)) {
            $address = '';
            foreach($params['street'] as $street){
                $address .=  ' '.$street;
            }

            $address = trim($address);
            $params['address'] = $address;
            if(!empty($params['country_id'])){
                $countryName = Mage::getModel('directory/country')->loadByCode($params['country_id'])->getName();
                $params['country'] = $countryName;
            }
            if (!empty($params['region_id'])) {
                $regionName        = Mage::getModel('directory/region')->load($params['region_id'])->getName();
                $params['region'] = $regionName;
            }
            /** @var  $helper Zitec_Dpd_Helper_Postcode_Search*/
            $helper = Mage::helper('zitec_dpd/postcode_search');
            $foundAddresses = $helper->search($params);
            if($foundAddresses !== null) {
                $foundAddresses = array(array('postcode' => $foundAddresses));
            } else {
                $foundAddresses = $helper->findAllSimilarAddressesForAddress($params);
            }
        } else {
            $foundAddresses = array();
        }
        $postcodes = array();
        foreach($foundAddresses as $foundAddress) {
            $postcodes[$foundAddress['postcode']] = $foundAddress;
        }
        $content = $this->getLayout()
            ->createBlock('zitec_dpd/adminhtml_shipment_postcode_autocompleter')
            ->setData('found_addresses',$postcodes)
            ->setTemplate('zitec_dpd/shipping/postcode/autocompleter.phtml')->toHtml();
        $this->getResponse()->setBody($content);

    }

    protected function validAddress($address) {
        return true;
    }
}
