<?php 
namespace Core\Classes;

class Session{
    public const PUT_GET_PREFIX = "put_get_";

    /**
     * ======================================================================
     *   Put Data
     * ======================================================================
     */
    public static function put(string $key, $value){
        @session_start(); // session may already started, put @
        $_SESSION[self::PUT_GET_PREFIX.$key] = $value;
    }

    /**
     * ======================================================================
     *   Get Data
     * ======================================================================
     */
    public static function get(string $key, $default_value=null, bool $flush = false){
        @session_start(); // session may already started, put @

        $sess_name = self::PUT_GET_PREFIX.$key;
        if(isset($_SESSION[$sess_name]) AND $_SESSION[$sess_name] != null)
        {
            $result =  $_SESSION[$sess_name];
            if($flush){
                $_SESSION[$sess_name] = null;
                unset($_SESSION[$sess_name]);
            }
            
            return $result;
        }
        return $default_value;
    }

}