<?php
class Itdelight_SeoRemarks_Block_Page_Switch extends Mage_Page_Block_Switch
{
    function removeParams($url, $params = []) {
        foreach ($params as $param) {
            $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*$/', '', $url);
            $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*&/', '$1', $url);
        }
        return $url;
    }
}