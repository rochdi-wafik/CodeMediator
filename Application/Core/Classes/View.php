<?php 
namespace Core\Classes;

use Core\Interfaces\IView;
use Core\Classes\SharedData;

class View implements IView{
    /**
     * =======================================================================
     * Constructor
     * =======================================================================
     * - Prevent create new instance of this class
     */
    private function __construct() {}


     /**
     * =======================================================================
     *   Show view
     * =======================================================================
     * - Show a view from `/views/..` there is no need to put ext .php
     * 
     * @example show simple view
     *    View::show('welcome');
     * @example Show view from subdirectory
     *    View::show('auth/login')
     * @example pass data to the view
     *    View::show('welcome', ['data' => 'Welcome Home'])
     * 
     */ 
    public static function show(string $view_name, ?array $data=[]){
               
        $file = trim(VIEWS.DS.$view_name, '.php').'.php';

        if(file_exists($file))
        {
            require_once $file ; 
            return true;
        }
        else{
            // Development Mode
            if(is_devmode())
            {
                echo show_msg("The view <a style='color:#2182f3'>$view_name</a> is not set<br>", DANGER);
            }else{
                echo page_404();
            }
            return false;
        }
    }
    // show alias
    public static function load(string $view_name, ?array $data=[]){
        self::show($view_name, $data);
    }


    /**
     * =======================================================================
     *   Is view exists
     * =======================================================================
     * - Check if a view is exists in /views directory
     * @example
     *    echo View::exists('welcome'); // 
     * @return bool true or false
     */
    public static function exists(string $view_name){
        $file = trim(VIEWS.DS.$view_name, '.php').'.php';
        return file_exists($file);
    }

    /**
     * =======================================================================
     * Set Data
     * =======================================================================
     * - Pass data to view
     * - Example: pass data from controller to view, or from route to view
     * - Key used to retrieve the data using getData
     */
    public static function setData($key, $value){
        //todo we can use SharedData
        SharedData::getInstance()->setData($key, $value);
    }

    /**
     * =======================================================================
     * Get Data
     * =======================================================================
     * - Get data that is set by setData()
     * - This function usually used by views to get data passed from outside
     * - But it still can be used between the functions inside same controller
     * @param key the same key used in setData()
     * @param default_value to be used if key not found
     */
    public static function getData(string $key, $default_value=null){
        return SharedData::getInstance()->getData($key, $default_value);
    }
}