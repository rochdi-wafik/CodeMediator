<?php 
namespace Core\Classes;

use Core\CodeMediator;
use ReflectionClass;
use ReflectionMethod;

/*
 *---------------------------------------------------------------
 * CodeMediator - Controller
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 *
 * This Is The Core Controller Of The MVC
 * All The Other Controllers Must Extends From This Class
 * 
 * --------------------------------------------------------------
 * [-] load()    method is using to call the view
 * [-] extract() method is using to convert array keys into variable
 *     and pass data to view as variables as vars
 * 
 * [-] get_instance() help us to get object from class
 *     and store it as a property, so that we don't need to get instance
 *     from a class every time we want to use a there methods,
 *     we can access the methods from the stored obj using $this->objName->method()
 *
 */
require_once CORE.DS.'Autoload.php';

class Controller{

    /**
     * =======================================================================
     * Constructor
     * =======================================================================
     * - Prevent create new instance of this class
     */
    private function __construct() {}

    /**
     * =======================================================================
     * Load Controller
     * =======================================================================
     * 
     * @example Load without action
     *    Controller::load(homeController::class);
     * 
     * @example Load With action
     *    Controller::load(homeController::class, "sayHello");
     * 
     * @example Load With action & single param
     *    Controller::load(homeController::class, "sayHello", "arg1");
     * 
     * @example Load With action & multi params
     *    Controller::load(homeController::class, "sayHello", ["arg1", "arg2"]);
     * 
     */
    public static function load( $className, $actionName=null, $provided_params=null){
        // check if class exists
        if(!class_exists(lcfirst($className)) && !class_exists(ucfirst($className))){
            if(is_devmode()){
                CodeMediator::show_error(array(
                    "title" => "Core Controller",
                    "content" => "Class $className is not exists",
                    "level" => DANGER,
                    "description" => 'you are trying to run a controller that is not exists using, if you thing the controller is exists, check the class name, double check if you have set the right route for the controller',
                ));
            }
            return;
        }

        // check if the class can be instantiated, 
        // it will check if the class has a constructor and it's not abstract.
        $reflectionClass = new ReflectionClass(ucfirst($className));
        if(!$reflectionClass->isInstantiable()){
            if(is_devmode()){
                CodeMediator::show_error(array(
                    "title" => "Core Controller",
                    "content" => "Class $className cannot be instantiated",
                    "level" => DANGER,
                    "description" => 'this can be happened if the class have private constructor or it is abstract class or interface',
                ));
            }
            return;
        }

        // create instance
        $instance = new $className();


        if($actionName==null){
            // echo "No params provided";
            return;
        }

        // Check if method exists
        if(!method_exists($instance, $actionName)){
            if(is_devmode()){
                CodeMediator::show_error(array(
                    "title" => "Core Controller",
                    "content" => "Action ( $actionName ) is not exists",
                    "level" => DANGER,
                    "description" => "the function $actionName() is not defined in the controller, make sure to create it. if the action is private, make it public",
                ));
            }
            return;
        }

        // before call method: check if no params provided
        if($provided_params==null){
            // if no params provided, but method need params, do not call
            if(self::is_action_require_params($instance, $actionName)){
                if(is_devmode()){
                    CodeMediator::show_error(array(
                        "title" => "Core Controller",
                        "content" => "Missing params for action ( $actionName )",
                        "level" => DANGER,
                        "description" => "the function $actionName() requires params, if params is optional, make sure to assign default value to the method params",
                    ));
                }
                return;
            }
            // method do not need params, call it
            $instance->$actionName();
        
            //call_user_func(array($instance, $actionName)); action already called, avoid duplication
            return;
        }

        /* Params provided,  check params if match the required*/
        self::execute_action_with_params($instance, $actionName, $provided_params);
    }


    /**
     * =======================================================================
     * Create Instance of a class
     * =======================================================================
     * - Create an instance of class by its string name,
     * - Add the instance to the this class properties
     * - So that we can access the created instance from this class properties
     * - We can create a set of instances
     * -------------------------------------------------------------------------
     * Example: 
     * - Get instance of a model
     * $this->get_instance('userModel'); 
     * $this->get_instance(array('userModel', 'postModel'))
     * - Access the model instance
     * $users = $this->userModel->getUsers();
     * 
     * @deprecated instance returned can't be detected by ide, use FooModel::getInstance() instead
     * 
     */
    protected function get_instance($className){
        /**
         * Set property named with className
         * set property's value as className instance
         */
        if(is_array($className))
        {
            foreach($className as $name){
                $this->{$name} = new  $name;
            }
        }
        else{
            $this->{$className} = new  $className;
        }  
    }


    /**
     * =======================================================================
     * Check if action require params
     * =======================================================================
     * 
     */
    private static function is_action_require_params($classInstance, $actionName, bool $including_optional_args = false){
        // Get action signature using  ReflectionMethod;
        $reflectionMethod = new ReflectionMethod($classInstance, $actionName);
        // Get action params
        $acceptedParameters = $reflectionMethod->getParameters();

        // IF params is null, means does'nt accept params
        if($acceptedParameters ==null){
            return false;
        }

        /* Method does accept params */
        

        // if all params are optional, means params not obligated
        foreach($acceptedParameters as $param){
            // param required
            if(!$param->isOptional()){
                return true;
            }
            // param optional: check if optional also must be checked
            else{
                if($including_optional_args){
                    return true;
                }
            }
        }
        // all is optional, means params not necessary
        return false;
    }

    /**
     * =======================================================================
     * Check if given params match the action params
     * =======================================================================
     * 
     */
    private static function execute_action_with_params($classInstance,  $actionName, $provided_params){
        // Get action signature using  ReflectionMethod;
        $reflectionMethod = new ReflectionMethod($classInstance, $actionName);
        // Get action params
        $parameters = $reflectionMethod->getParameters();

        // IF params is null, means doesn't accept params
        if($parameters ==null){
            return false;
        }

        // number of args user provided
        $providedParamsCount= is_array($provided_params) ? count($provided_params) : 1;

        // count how many args the action is required (exclude optional params)
        $requiredParamsCount = 0;
        foreach ($parameters as $parameter) {
            if (!$parameter->isOptional()) {
                $requiredParamsCount++;
            }
        }

        // Method accept more than the provided args
        if ($providedParamsCount < $requiredParamsCount) {
            // Not enough arguments provided for method $actionName
            if(is_devmode()){
                CodeMediator::show_error(array(
                    "title" => "Core Controller",
                    "content" => "No enough params provided for action $actionName",
                    "level" => DANGER,
                    "description" => "the function $actionName()  requires more params than the provided, if params is optional, make sure to assign default value to the method params",
                ));
            }
            return;
        }

        // call the action with params
        if(is_array($provided_params)){
            // case multiple params needed
            call_user_func_array(array($classInstance, $actionName), $provided_params);
        }
        else{
            // case only single params needed
            call_user_func(array($classInstance, $actionName), $provided_params);
        }

    }
}