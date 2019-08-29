<?php

require_once(dirname(__FILE__) . "/exceptions/RequestException.php");

class AC_Connector
{

    public $url;
    public $api_key;
    public $output = "json";

    function __construct($url, $api_key, $api_user = "", $api_pass = "")
    {
        // $api_pass should be md5() already
        $base = "";
        if (!preg_match("/https:\/\/www.activecampaign.com/", $url)) {
            // not a reseller
            $base = "/admin";
        }

        if (preg_match("/\/$/", $url)) {
            // remove trailing slash
            $url = substr($url, 0, strlen($url) - 1);
        }

        if ($api_key) {
            $this->url = "{$url}{$base}/api.php?api_key={$api_key}";
        } elseif ($api_user && $api_pass) {
            $this->url = "{$url}{$base}/api.php?api_user={$api_user}&api_pass={$api_pass}";
        }

        $this->api_key = $api_key;
    }

    /**
     * @return  boolean  Whether or not the API credentials are valid.
     *
     * Tests the API URL and key using the user_me API method.
     */
    public function credentials_test()
    {
        $test_url = "{$this->url}&api_action=user_me&api_output={$this->output}";
        $r = $this->curl($test_url);
        if (is_object($r) && (int)$r->result_code) {
            // successful
            $r = true;
        } else {
            // failed - log it
            $this->curl_response_error = $r;
            $r = false;
        }

        return $r;
    }

    /**
     * @param  string  url            The API URL with the relevant method params.
     * @param  array   params_data    The GET or POST parameters (keys and values).
     * @param  string  verb           The HTTP verb (GET, POST, DELETE, etc).
     * @param  string  custom_method  Any custom method that gets handled differently (such as how we process the response).
     * @return object                 The response object from the curl request.
     */
    public function curl($url, $params_data = array(), $verb = "", $custom_method = "")
    {
        if ($this->version == 1) {
            // find the method from the URL.
            $method = preg_match("/api_action=[^&]*/i", $url, $matches);
            if ($matches) {
                $method = preg_match("/[^=]*$/i", $matches[0], $matches2);
                $method = $matches2[0];
            } elseif ($custom_method) {
                $method = $custom_method;
            }
        } elseif ($this->version == 2) {
            $method = $custom_method;
            $url .= "?api_key=" . $this->api_key;
        }

        $request = curl_init();
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        if ($params_data && $verb == "GET") {
            if ($this->version == 2) {
                $url .= "&" . $params_data;
                curl_setopt($request, CURLOPT_URL, $url);
            }
        } else {
            curl_setopt($request, CURLOPT_URL, $url);
            if ($params_data && !$verb) {
                // if no verb passed but there IS params data, it's likely POST.
                $verb = "POST";
            } elseif ($params_data && $verb) {
                // $verb is likely "POST" or "PUT".
            } else {
                $verb = "GET";
            }
        }

        if ($verb == "POST" || $verb == "PUT" || $verb == "DELETE") {
            if ($verb == "PUT") {
                curl_setopt($request, CURLOPT_CUSTOMREQUEST, "PUT");
            } elseif ($verb == "DELETE") {
                curl_setopt($request, CURLOPT_CUSTOMREQUEST, "DELETE");
            } else {
                $verb = "POST";
                curl_setopt($request, CURLOPT_POST, 1);
            }

            $data = "";
            if (is_array($params_data)) {
                foreach ($params_data as $key => $value) {
                    if (is_array($value)) {
                        if (is_int($key)) {
                            // array two levels deep
                            foreach ($value as $key_ => $value_) {
                                if (is_array($value_)) {
                                    foreach ($value_ as $k => $v) {
                                        $k = urlencode($k);
                                        $data .= "{$key_}[{$key}][{$k}]=" . urlencode($v) . "&";
                                    }
                                } else {
                                    $data .= "{$key_}[{$key}]=" . urlencode($value_) . "&";
                                }
                            }
                        } elseif (preg_match('/^field\[.*,0\]/', $key)) {
                            // if the $key is that of a field and the $value is that of an array
                            if (is_array($value)) {
                                // then join the values with double pipes
                                $value = implode('||', $value);
                            }

                            $data .= "{$key}=" . urlencode($value) . "&";
                        } else {
                            // IE: [group] => array(2 => 2, 3 => 3)
                            // normally we just want the key to be a string, IE: ["group[2]"] => 2
                            // but we want to allow passing both formats
                            foreach ($value as $k => $v) {
                                if (!is_array($v)) {
                                    $k = urlencode($k);
                                    $data .= "{$key}[{$k}]=" . urlencode($v) . "&";
                                }
                            }
                        }
                    } else {
                        $data .= "{$key}=" . urlencode($value) . "&";
                    }
                }
            } else {
                // not an array - perhaps serialized or JSON string?
                // just pass it as data
                $data = "data={$params_data}";
            }

            $data = rtrim($data, "& ");
            curl_setopt($request, CURLOPT_HTTPHEADER, array("Expect:"));
            curl_setopt($request, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($request);
        $curl_error = curl_error($request);
        if (!$response && $curl_error) {
            return $curl_error;
        }

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
        if (!preg_match("/^[2-3][0-9]{2}/", $http_code)) {
            // If not 200 or 300 range HTTP code, return custom error.
            return "HTTP code $http_code returned";
        }

        curl_close($request);
        $object = json_decode($response);

        if (!is_object($object) || (!isset($object->result_code) && !isset($object->succeeded) && !isset($object->success))) {
            // add methods that only return a string
            $string_responses = array("tags_list", "segment_list", "tracking_event_remove", "contact_list", "form_html", "tracking_site_status", "tracking_event_status", "tracking_whitelist", "tracking_log", "tracking_site_list", "tracking_event_list");
            if (in_array($method, $string_responses)) {
                return $response;
            }

            $requestException = new RequestException;
            $requestException->setFailedMessage($response);
            throw $requestException;
        }

        $object->http_code = $http_code;

        if (isset($object->result_code)) {
            $object->success = $object->result_code;
            if (!(int)$object->result_code) {
                $object->error = $object->result_message;
            }
        } elseif (isset($object->succeeded)) {
            // some calls return "succeeded" only
            $object->success = $object->succeeded;
            if (!(int)$object->succeeded) {
                $object->error = $object->message;
            }
        }

        return $object;
    }

}
