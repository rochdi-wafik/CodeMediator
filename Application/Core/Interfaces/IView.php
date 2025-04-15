<?php
namespace Core\Interfaces;

interface IView{

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
    public static function show(string $view_name, ?array $data=[]);


    /**
     * =======================================================================
     *   Is view exists
     * =======================================================================
     * - Check if a view is exists in /views directory
     * @example
     *    echo View::exists('welcome'); // 
     * @return bool true or false
     */
    public static function exists(string $view_name);

    /**
     * =======================================================================
     * Get Data
     * =======================================================================
     * Get data passed to this view
     */
    public static function getData(string $key);
}