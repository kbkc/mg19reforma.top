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
class Zitec_Dpd_Block_Postcode_Autocompleter extends Mage_Core_Block_Template
{

    /**
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_showBlock()) {
            return $this->_getHtml($this->getFieldNames(), $this->getSections());
        } else {
            return '';
        }
    }
    /**
     *
     * @param array $fieldNames
     *
     * @return string
     */
    protected function _getHtml(array $fieldNames, array $sectionNames)
    {
        if (!$fieldNames || !$sectionNames) {
            return '';
        }
        $fieldsHtml = '';
        $fieldsHtml .= "
            fields['generic'] = [];
            fields['sections'].push('generic');
        ";
        foreach($sectionNames as $sectionName) {
            $fieldsHtml .= "
                fields['{$sectionName}'] = [];
                fields['sections'].push('{$sectionName}');
            ";
            foreach ($fieldNames as $fieldName) {
                $fieldsHtml .= "
                field = $('{$sectionName}:{$fieldName}');
                if (field) {
                    fields['{$sectionName}'].push(field);
                } else {
                    field = $('{$fieldName}');
                    if(field) {
                        fields['generic'].push(field);
                    }
                }";
            }

        }
        $loadingImageUrl =  $this->getSkinUrl('images/ajax-loader.gif');
        $loadingText = $this->_getHelper()->__('Loading');
        // TODO Zitec Set the country in the config file
        $country =  'RO';
        $url = '/zitec_dpd/shipment/validatePostcode';
        $html = "
<script type='text/javascript'>

    var className = '{$this->getClassName()}';
    var fields = {'sections' : []};
    var field;
    //var fields = [];
    {$fieldsHtml}
    var options = {
        loadingImageUrl: '{$loadingImageUrl}',
        loadingText: '{$loadingText}',
        country: '{$country}',
        url: '{$url}'
    }
    new zitecPostcodeAutocompleter.Autocompleter(fields, options);
</script>";

        return $html;
    }

    /**
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array("street1", "street2", "city", "region_id", "country_id", 'street_1', 'street_2');
    }

    public function getSections() {
        return array('billing', 'shipping');
    }



    /**
     *
     * @return boolean
     */
    protected function _showBlock()
    {
        return $this->_getHelper()->moduleIsActive();
    }

    /**
     *
     * @return Zitec_Dpd_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('zitec_dpd');
    }


}

