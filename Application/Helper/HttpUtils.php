<?php

use Core\DatabaseHandler;

class HttpUtils extends DatabaseHandler{

    /**
     * ==============================================================
     *  Get/Parse Form Data
     * ==============================================================
     * - When we want to send data to server, using POST or PUT etc,
     *   There are two content types we can choose between:
     * [1] application/json; charset=utf-8
     * - This method send the data in stringified json, like this:
     *   {"Name": "John Smith", "Age": 23}
     *  
     * [2] application/x-www-form-urlencoded
     * - This method send the data in url encoding format, like this:
     *   "Name=John+Smith&Age=23"
     * ----------------------------------------------------------------
     * - Each method of above has a way to parse the data to array/obj
     * [1] To parse application/json, we use:
     * $data = json_decode(file_get_contents('php://input'), true);
     * echo $data['name'];
     * 
     * [2] To parse application/x-www-form-urlencoded, we use:
     * parse_str(file_get_contents('php://input'), $data);
     * echo $data['name'];
     * ----------------------------------------------------------------
     * - This method will help us to accept both approaches, and it will
     *   return the response as array or json
     * ------------------------------------------------------------------
     * @deprecated
     * - This method use ContentType to check the data type is json or
     *   form encoded.
     * - This work, but if user forgot to specify the contentType in his
     *   Request, the method will not works
     */
    public static function parse_request_data2($to_array=true){
        // [-] Get the raw request body
        $request_body = file_get_contents('php://input');
        // [-] Check content-type
        $content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
        // Case application/json
        if (strpos($content_type, 'application/json') !== false) {
            $data = json_decode($request_body, true);
        } 
        // Case application/x-www-form-urlencoded
        else {
            $data = [];
            parse_str($request_body, $data);
        }
        // Return parsed 
        return $to_array ? $data : json_encode($data);
    }


    /**
     * ==============================================================
     *  Get/Parse Form Data
     * ==============================================================
     * - When we want to send data to server, using POST or PUT etc,
     *   There are two content types we can choose between:
     * [1] application/json; charset=utf-8
     * - This method send the data in stringified json, like this:
     *   {"Name": "John Smith", "Age": 23}
     *  
     * [2] application/x-www-form-urlencoded
     * - This method send the data in url encoding format, like this:
     *   "Name=John+Smith&Age=23"
     * -------------------------[Parse Data]-------------------------------
     * - We can just check the contentType and parse the data depend on that.
     * - But we may forget to specify the contentType, so this will not work.
     * - Instead, We can try to check the data type itself, this can be done
     *   by trying to parse the data to json, if no error: means contentType
     *   is json, if there is error, means its not json, so we'll try to
     *   parse it as form encoded
     * 
     */ 
    public static function parse_request_data($to_array = false) {
        // [-] Get the raw request body
        $request_body = file_get_contents('php://input');

        // [-] Try to decode to json 
        $data = json_decode($request_body, $to_array);

        // Valid Json: means JsonData (application/json)
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data; // Valid JSON
        } 
        // Invalid Json: means FormData (application/x-www-form-urlencoded)
        else {
            // try urlencoded
            $data = [];
            parse_str($request_body, $data);

            // Check if parse_str produced any data.
            if (!empty($data)) {
                return $to_array ? $data : (object) $data;
            } 
            // invalid data
            else {
                return $to_array ? [] : (object) null;
            }
        }
    }


        
    /**
     * ==============================================================================
     * Get HTTP Bearer Token
     * ==============================================================================
     * Get Bearer Token From HTTP Authorization
     * 
     * - As the HTTP_AUTHORIZATION key is not always present in the $_SERVER super-global array,
     * - We can use the apache_request_headers() function to get all headers.
     * - This is less-chance needed, as the above function is enough.
     */
    public static function getBearerToken(){
        $headers = [];
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = $_SERVER;
        }

        if (isset($headers['Authorization']) && preg_match('/Bearer\s+(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
        return null;
    }


}