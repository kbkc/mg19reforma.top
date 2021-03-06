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
 * Video view block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Video_View extends Mage_Core_Block_Template
{
    /**
     * get the current video
     *
     * @access public
     * @return mixed (Istans_Nailgallery_Model_Video|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentVideo()
    {
        return Mage::registry('current_video');
    }
}
