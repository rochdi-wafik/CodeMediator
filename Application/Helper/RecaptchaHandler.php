<?php 
class RecaptchaHandler{
    // Verifying the user's response (https://developers.google.com/recaptcha/docs/verify)
    public const API_ENDPOINT = "https://www.google.com/recaptcha/api/siteverify";
    public $lastError=null;
    public $apiResult=null;
    public $secret_key;

    /**
     * ===================================================================
     * Constructor
     * ===================================================================
     * @param secret_key (required)
     */
    public function __construct(string $secret_key) {
        $this->secret_key = $secret_key;
    }

    /**
     * ===================================================================
     *  Verify Captcha 
     * ===================================================================
     * @param response (required) The captcha client response
     * @param remote_id (optional) The user's IP address.
     */
    function verify_recaptcha(string $response, $remote_ip=null){
        // Build HTTP Headers
        $http_headers = array(
            'Accept: application/json', 
            'Content-type: application/x-www-form-urlencoded'
        );
        
        // Build POST Form
        $post_data = http_build_query(array(
                'secret' => $this->secret_key,
                'response' => $response,
                'remoteip' => $remote_ip
        ));
        
        // Send data on using cUrl
        $response=null;
        if(function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec')) {
            // Use cURL to get data 10x faster than using file_get_contents or other methods
            $ch =  curl_init(self::API_ENDPOINT);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
                $response = curl_exec($ch);
                curl_close($ch);
        } 
        // IF cURL not enabled, use file_get_contents
        else {
            $opts = array('http' =>[
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $post_data
            ]);
            
            $context  = stream_context_create($opts);
            $response = file_get_contents(self::API_ENDPOINT, false, $context);
        }

        // Verify api response
        if($response) {
            $this->apiResult = json_decode($response, true);
            if ($this->apiResult['success']===true) {
                return true;
            } else {
                $this->lastError = "Captcha verification failed";
                return false;
            }
        }

        // Dead end
        $this->lastError = "Unable to get response from server";
        return null; 
    }

    /**
     * ===================================================================
     *  Verify Captcha V3
     * ===================================================================
     * @param captcha_response (required) The captcha client response
     * @param remote_id (optional) The user's IP address.
     * @return float scores: from 0.1 to 1, highest score more security.
     */
    function verify_recaptchaV3(string $captcha_response,  $remote_ip=null): ?float{
        // Build HTTP Headers
        $http_headers = array(
            'Accept: application/json', 
            'Content-type: application/x-www-form-urlencoded'
        );
        
        // Build POST Form
        $post_data = http_build_query(array(
                'secret' => $this->secret_key,
                'response' => $captcha_response,
                'remoteip' => $remote_ip
        ));
        
        // Send data on using cUrl
        $response=null;
        if(function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec')) {
            // Use cURL to get data 10x faster than using file_get_contents or other methods
            $ch =  curl_init(self::API_ENDPOINT);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
                $response = curl_exec($ch);
                curl_close($ch);
        } 
        // IF cURL not enabled, use file_get_contents
        else {
            $opts = array('http' =>[
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $post_data
            ]);
            
            $context  = stream_context_create($opts);
            $response = file_get_contents(self::API_ENDPOINT, false, $context);
        }

        // Verify api response
        if($response) {
            $this->apiResult = json_decode($response, true);
            if ($this->apiResult['success']===true) {
                return true;
            } else {
                $this->lastError = "Captcha verification failed";
                return false;
            }
        }

        // Verify Response
        if($response){
            $this->apiResult = json_decode($response, true);
            if ($this->apiResult["success"] == true && ! empty($this->apiResult["action"])) {
                return $this->apiResult["score"];
            }
        }

        // Dead end
        $this->lastError = "Unable to get response from server";
        return null; 
    }
}