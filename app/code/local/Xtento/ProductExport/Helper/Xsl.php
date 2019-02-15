<?php

/**
 * Product:       Xtento_ProductExport (1.9.15)
 * ID:            9rv0KxEzsnudefqWcvJP2ekOKTyiO0t9CNEA/UPa0K0=
 * Packaged:      2018-03-21T11:37:30+00:00
 * Last Modified: 2017-11-28T13:14:43+01:00
 * File:          app/code/local/Xtento/ProductExport/Helper/Xsl.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Helper_Xsl extends Mage_Core_Helper_Abstract
{
    // IMPORTANT: Remember to add your custom function into allowed functions in Model/Output/Xsl.php in $allowedPhpFunctions!

    // Static functions which can be called in the XSL Template, example:
    // <xsl:value-of select="php:functionString('Xtento_ProductExport_Helper_Xsl::mb_sprintf', '%015.0f', price)"/>
    // <xsl:value-of select="php:functionString('Xtento_ProductExport_Helper_Xsl::currencyByStore', price, store_id, 0)"/>
    // <xsl:value-of select="php:functionString('Xtento_ProductExport_Helper_Xsl::resizeImage', 'image', image_raw, 200, 200)"/>

    // Get product price for customer group
    static function getProductPriceForCustomerGroup($productId, $customerGroupId, $storeId = null)
    {
        $groupPrice = 0;
        if ($storeId !== null) {
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
        } else {
            $product = Mage::getModel('catalog/product')->load($productId);
        }
        if ($product->getId()) {
            $product->setCustomerGroupId($customerGroupId);
            $groupPrice = $product->getPriceModel()->getFinalPrice(1, $product);
        }
        return (float)$groupPrice;
    }

    // Resize image
    static function resizeImage($imageType, $imageFile, $width, $height = null)
    {
        try {
            return (string)Mage::helper('catalog/image')->init(
                Mage::getSingleton('catalog/product'),
                $imageType,
                $imageFile
            )->resize($width, $height);
        } catch (Exception $e) {
            $imageFile = Mage::getDesign()->getSkinUrl(
                'images/catalog/product/placeholder/image.jpg',
                array('_area' => 'frontend')
            );
            return (string)Mage::helper('catalog/image')->init(
                Mage::getSingleton('catalog/product'),
                $imageType,
                $imageFile
            )->resize($width, $height);
        }
    }

    // sprintf with multibyte support
    static function mb_sprintf($format)
    {
        $argv = func_get_args();
        array_shift($argv);
        return self::mb_vsprintf($format, $argv);
    }

    /**
     * Works with all encodings in format and arguments.
     * Supported: Sign, padding, alignment, width and precision.
     * Not supported: Argument swapping.
     */
    static function mb_vsprintf($format, $argv, $encoding = "UTF-8")
    {
        if (isset($argv[0]) && (is_numeric($argv[0]) || is_float($argv[0]))) {
            return vsprintf($format, $argv);
        }

        if (is_null($encoding))
            $encoding = mb_internal_encoding();

        // Use UTF-8 in the format so we can use the u flag in preg_split
        $format = mb_convert_encoding($format, 'UTF-8', $encoding);

        $newformat = ""; // build a new format in UTF-8
        $newargv = array(); // unhandled args in unchanged encoding

        while ($format !== "") {

            // Split the format in two parts: $pre and $post by the first %-directive
            // We get also the matched groups
            list ($pre, $sign, $filler, $align, $size, $precision, $type, $post) =
                preg_split("!\%(\+?)('.|[0 ]|)(-?)([1-9][0-9]*|)(\.[1-9][0-9]*|)([%a-zA-Z])!u",
                    $format, 2, PREG_SPLIT_DELIM_CAPTURE);

            $newformat .= mb_convert_encoding($pre, $encoding, 'UTF-8');

            if ($type == '') {
                // didn't match. do nothing. this is the last iteration.
            } elseif ($type == '%') {
                // an escaped %
                $newformat .= '%%';
            } elseif ($type == 's') {
                $arg = array_shift($argv);
                $arg = mb_convert_encoding($arg, 'UTF-8', $encoding);
                $padding_pre = '';
                $padding_post = '';

                // truncate $arg
                if ($precision !== '') {
                    $precision = intval(substr($precision, 1));
                    if ($precision > 0 && mb_strlen($arg, $encoding) > $precision)
                        $arg = mb_substr($arg, 0, $precision, $encoding);
                }

                // define padding
                if ($size > 0) {
                    $arglen = mb_strlen($arg, $encoding);
                    if ($arglen < $size) {
                        if ($filler === '')
                            $filler = ' ';
                        if ($align == '-')
                            $padding_post = str_repeat($filler, $size - $arglen);
                        else
                            $padding_pre = str_repeat($filler, $size - $arglen);
                    }
                }

                // escape % and pass it forward
                $newformat .= $padding_pre . str_replace('%', '%%', $arg) . $padding_post;
            } else {
                // another type, pass forward
                $newformat .= "%$sign$filler$align$size$precision$type";
                $newargv[] = array_shift($argv);
            }
            $format = strval($post);
        }
        // Convert new format back from UTF-8 to the original encoding
        $newformat = mb_convert_encoding($newformat, $encoding, 'UTF-8');
        return vsprintf($newformat, $newargv);
    }

    static function currencyByStore($value, $store = null, $format = true)
    {
        return Mage::helper('core')->currencyByStore($value, $store, $format, false);
    }

    /**
     * @param $value
     * @param $fromCurrency
     * @param null $toCurrency
     * @return mixed
     *
     * If this function returns a blank page/errors out, the currency rate doesn't exist in Magento probably.
     */
    static function currencyConvert($value, $fromCurrency, $toCurrency = null)
    {
        return Mage::helper('directory')->currencyConvert($value, $fromCurrency, $toCurrency);
    }
}