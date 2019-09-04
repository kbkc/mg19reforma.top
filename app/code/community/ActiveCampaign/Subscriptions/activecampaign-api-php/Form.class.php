<?php

class AC_Form extends ActiveCampaign
{

    public $version;
    public $url_base;
    public $url;
    public $api_key;

    function __construct($version, $url_base, $url, $api_key)
    {
        $this->version = $version;
        $this->url_base = $url_base;
        $this->url = $url;
        $this->api_key = $api_key;
    }

    function getforms($params)
    {
        $request_url = "{$this->url}&api_action=form_getforms&api_output={$this->output}";
        $response = $this->curl($request_url);
        return $response;
    }

}
