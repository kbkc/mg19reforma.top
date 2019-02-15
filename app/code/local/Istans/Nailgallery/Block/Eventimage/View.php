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
 * Event image view block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Eventimage_View extends Mage_Core_Block_Template
{
    /**
     * get the current event image
     *
     * @access public
     * @return mixed (Istans_Nailgallery_Model_Eventimage|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentEventimage()
    {
        return Mage::registry('current_eventimage');
    }
}
