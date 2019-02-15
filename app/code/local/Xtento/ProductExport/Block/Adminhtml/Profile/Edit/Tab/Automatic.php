<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2013-02-10T18:57:17+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Edit/Tab/Automatic.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Edit_Tab_Automatic extends Xtento_ProductExport_Block_Adminhtml_Widget_Tab
{
    protected function _prepareForm()
    {
        $model = Mage::registry('product_export_profile');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('cronjob_fieldset', array(
            'legend' => Mage::helper('xtento_productexport')->__('Cronjob Export'),
            'class' => 'fieldset-wide',
        ));

        $fieldset->addField('cronjob_note', 'note', array(
            'text' => Mage::helper('xtento_productexport')->__('<strong>Important</strong>: To use cron job exports, please make sure the Magento cronjob has been set up as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob" target="_blank">here</a>.')
        ));

        if (Mage::helper('xtcore/utils')->isCronRunning()) {
            $model->setCronjobStatus(Mage::helper('xtento_productexport')->__("Cron seems to be running properly. Seconds since last execution: %d", (time() - Mage::helper('xtcore/utils')->getLastCronExecution())));
            $note = '';
        } else {
            if ((time() - Mage::helper('xtcore/data')->getInstallationDate()) > (60 * 30)) { // Module was not installed within the last 30 minutes
                if (Mage::helper('xtcore/utils')->getLastCronExecution() == '') {
                    $model->setCronjobStatus(Mage::helper('xtento_productexport')->__("Cron.php doesn't seem to be set up at all. Cron did not execute within the last 15 minutes."));
                    $note = Mage::helper('xtento_productexport')->__('Please make sure to set up the cronjob as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob" target="_blank">here</a> and check the cron status 15 minutes after setting up the cronjob properly again.');
                } else {
                    $model->setCronjobStatus(Mage::helper('xtento_productexport')->__("Cron.php doesn't seem to be set up properly. Cron did not execute within the last 15 minutes."));
                    $note = Mage::helper('xtento_productexport')->__('Please make sure to set up the cronjob as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob" target="_blank">here</a> and check the cron status 15 minutes after setting up the cronjob properly again.');
                }
            } else {
                $model->setCronjobStatus(Mage::helper('xtento_productexport')->__("Cron status wasn't checked yet. Please check back in 30 minutes."));
                $note = Mage::helper('xtento_productexport')->__('Please make sure to set up the cronjob as explained <a href="http://support.xtento.com/wiki/Setting_up_the_Magento_cronjob" target="_blank">here</a> and check the cron status 15 minutes after setting up the cronjob properly again.');
            }
        }
        $fieldset->addField('cronjob_status', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Cronjob Status'),
            'name' => 'cronjob_status',
            'disabled' => true,
            'note' => $note,
            'value' => $model->getCronjobStatus()
        ));

        $fieldset->addField('cronjob_enabled', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Enable Cronjob Export'),
            'name' => 'cronjob_enabled',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        $fieldset->addField('cronjob_frequency', 'select', array(
            'label' => Mage::helper('xtento_productexport')->__('Export Frequency'),
            'name' => 'cronjob_frequency',
            'values' => Mage::getModel('xtento_productexport/system_config_source_cron_frequency')->toOptionArray(),
            'note' => Mage::helper('xtento_productexport')->__('How often should the export be executed?')
        ));

        $fieldset->addField('cronjob_custom_frequency', 'text', array(
            'label' => Mage::helper('xtento_productexport')->__('Custom Export Frequency'),
            'name' => 'cronjob_custom_frequency',
            'note' => Mage::helper('xtento_productexport')->__('A custom cron expression can be entered here. Make sure to set "Cronjob Frequency" to "Use custom frequency" if you want to enter a custom cron expression here. To set up multiple cronjobs, separate multiple cron expressions by a semi-colon ; Example: */5 * * * *;0 3 * * * '),
            'class' => 'validate-cron',
            'after_element_html' => $this->_getCronValidatorJs()
        ));

        $exportEvents = Mage::getModel('xtento_productexport/system_config_source_export_events')->toOptionArray(Mage::registry('product_export_profile')->getEntity());
        if (count($exportEvents) > 0) {
            $fieldset = $form->addFieldset('event_fieldset', array(
                'legend' => Mage::helper('xtento_productexport')->__('Event-based Export'),
                'class' => 'fieldset-wide',
            ));

            $fieldset->addField('event_note', 'note', array(
                'text' => Mage::helper('xtento_productexport')->__('If you want to initiate the export directly after a certain event has been dispatched in Magento, select the appropriate events here. One export per event will be created.')
            ));

            $form->setValues($model->getData());

            $fixedArray = $model->getEventObservers();
            if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.4.0.0', '<')) {
                // Fixing a bug in array_search for checkboxes data type that was fixed in Mage 1.4
                array_unshift($fixedArray, '---');
            }

            $fieldset->addField('event_observers', 'checkboxes', array(
                'label' => Mage::helper('xtento_productexport')->__('Export Events'),
                'name' => 'event_observers[]',
                'values' => $exportEvents,
                'value' => $fixedArray,
                'checked' => $fixedArray,
                'after_element_html' => '<small>Please click <a href="http://support.xtento.com/wiki/Magento_Extensions:Magento_Product_Export_Module#Event-based_Export" target="_blank">here</a> to learn more about event-based export.</small>',
            ));
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function _getCronValidatorJs()
    {
        $errorMsg = Mage::helper('xtento_productexport')->__('This is no valid cron expression.');
        $js = <<<EOT
<script>
Validation.add('validate-cron', '{$errorMsg}', function(v) {
     if (v == "") {
        return true;
     }
     return RegExp("^[-0-9,*/; ]+$","gi").test(v);
});
</script>
EOT;

        return $js;
    }
}