<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2017-02-13T17:04:11+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Export/Data/Review/General.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Export_Data_Review_General extends Xtento_ProductExport_Model_Export_Data_Product_General
{
    public function getConfiguration()
    {
        return array(
            'name' => 'General review information',
            'category' => 'Review',
            'description' => 'Export extended review information.',
            'enabled' => true,
            'apply_to' => array(Xtento_ProductExport_Model_Export::ENTITY_REVIEW),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray; // Write directly on category level
        // Fetch fields to export
        $review = $collectionItem->getReview();

        // Timestamps of creation/update
        if ($this->fieldLoadingRequired('created_at_timestamp')) $this->writeValue('created_at_timestamp', Mage::helper('xtento_productexport/date')->convertDateToStoreTimestamp($review->getCreatedAt()));

        // Which line is this?
        $this->writeValue('line_number', $collectionItem->_currItemNo);
        $this->writeValue('count', $collectionItem->_collectionSize);

        // Export information
        $this->writeValue('export_id', (Mage::registry('product_export_log')) ? Mage::registry('product_export_log')->getId() : 0);

        foreach ($review->getData() as $key => $value) {
            if ($key == 'entity_id') {
                continue;
            }
            if (!$this->fieldLoadingRequired($key)) {
                continue;
            }
            $this->writeValue($key, $value);
        }

        // Add rating
        if ($this->fieldLoadingRequired('product_rating')) {
            $voteValues = array();
            foreach ($review->getRatingVotes() as $vote) {
                $voteValues[] = $vote->getValue();
            }
            $averageRating = 0;
            if (count($voteValues) > 0) {
                $averageRating = array_sum($voteValues) / count($voteValues);
            }
            $this->writeValue('product_rating', $averageRating);
        }

        // Review link
        if ($this->fieldLoadingRequired('review_link')) {
            $reviewLink = Mage::getUrl('review/product/view', array('id' => $review->getReviewId(), '_store' => $this->getStoreId()));
            $this->writeValue('review_link', $reviewLink);
        }

        // Total reviews
        if ($this->fieldLoadingRequired('total_reviews')) {
            $collection = Mage::getModel('review/review')->getResourceCollection()
                ->addFieldToFilter('main_table.entity_pk_value', $review->getEntityPkValue())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED);
            if ($this->getStoreId()) {
                $collection->addStoreFilter($this->getStoreId());
            }
            $this->writeValue('total_reviews', $collection->count());
        }

        $originalWriteArray = & $this->_writeArray;
        // Add product information
        $productId = $review->getEntityPkValue();
        if ($productId > 0) {
            $product = Mage::getModel('catalog/product')->load($productId);
            if ($product->getId()) {
                if ($this->getStoreId()) {
                    $product->setStoreId($this->getStoreId());
                }
                $this->_writeArray = & $returnArray['product'];
                $this->_exportProductData($product, $this->_writeArray);
                $this->writeValue('entity_id', $product->getId());
                $this->_writeArray = & $originalWriteArray;
            }
        }

        // Done
        return $returnArray;
    }
}