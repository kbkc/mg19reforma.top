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
 * Video widget block
 *
 * @category    Istans
 * @package     Istans_Nailgallery
 * @author      Ultimate Module Creator
 */
class Istans_Nailgallery_Block_Video_Widget_View extends Mage_Core_Block_Template implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'istans_nailgallery/video/widget/view.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return Istans_Nailgallery_Block_Video_Widget_View
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $videoId = $this->getData('video_id');
        if ($videoId) {
            $video = Mage::getModel('istans_nailgallery/video')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($videoId);
            if ($video->getStatus()) {
                $this->setCurrentVideo($video);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}
