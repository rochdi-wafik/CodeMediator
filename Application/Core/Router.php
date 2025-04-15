<?php // Last Edit : 09-04-2025: Fix case-sensitive
namespace Core;

use Core\Classes\Controller;
use Core\Classes\Route;
use Core\Interfaces\IMiddleware;

/*
 *---------------------------------------------------------------
 * CodeMediator - Router
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File. 
 *
 * 
 * @todo User routes are executed by Route.php, but!
 *       Make sure to let Router.php execute the user routes.
 *       Route.php should only eval the routes and store them in list[] 
 *      
 */
class Router
{
    // Default URL Segments
    private $uri_controller = null; // @todo call strings from const or class
    private $uri_action     = null; // @todo call strings from const or class
    private $uri_params     = [];

    // Constants
    public const DEFAULT_CONTROLLER = 'indexController';
    public const DEFAULT_ACTION = 'default';
    public const CONTROLLER_SUFFIX = "Controller"; 

    /**
     * =====================================================================
     *   Constructor
     * =====================================================================
     * [1] Initialize default Controller & action
     * - set default controller and action in case no URI present in the URL
     * - Example: HomeController{}->default()
     * [2] Dispatch
     * - Match Request URI with the appropriate Controller/action/params
     * - First check if the request mapped in User routes in config/routes.php 
     * - IF not: execute the default routing
     */ 
    function __construct()
    {
        // [-] Specify default controller
        $this->uri_controller = self::getDefaultController();
        
        // [-] Specify default action
        $this->uri_action = self::getDefaultAction();

        $this->dispatch();
    }

    /**
     * =====================================================================
     *   Parse Request URL
     * =====================================================================
     * - IF Request Url Is: `http://site.com/user/posts/5`
     * - Then extract the URI parts: `/user/posts/5`
     * - Convert the URI to list: ['user', 'posts', '5']
     * - Specify the default controller, action, params:
     *   $ First segment `user` will be the controller.
     *   $ Second segment `posts` will be the action.
     *   $ Rest of segments `['5']` will be the params
     */  
    private function parseUrl(): ?string
    {
        $url = URI::getRequestUri();
        $url = strtolower($url);

        // decode url in case has non-latin chars like arabic
        if(verify_config('url_decode', true))
        {
            $url = urldecode($url); 
        }
        
        // parse url
        $request_uri = parse_url($url,PHP_URL_PATH); // filter queries (?q=..)
        
        // split url to parts
        $explode = explode('/', $request_uri);

        //------------------------------------------------
        if(isset($explode[0]) && $explode[0] != '')
        {
            // add Controller suffix. i.e: if `/blog` => `blogController`
            $this->uri_controller = $explode[0]. self::CONTROLLER_SUFFIX;
        }
        //------------------------------------------------
        if(isset($explode[1]) && $explode[1] != '')
        {
            $this->uri_action = $explode[1]; 
        }
        //------------------------------------------------
        if(isset($explode[2]) && $explode[2] != '')
        {
            $this->uri_params = array_slice($explode, 2);
        }

        return $request_uri;

    }

    /**
     * =====================================================================
     *   Dispatch
     * =====================================================================
     * - Perform the default routing:
     * [1] Parse the request url to extract the URI
     *     parseUrl() will extract the default controller, action, params
     * [2] Check if the request uri not specified in custom routes.
     * [3] Load the controller, call action, pass params
     * ----------------------------------------------------------------------
     * - Assume Request Url Is: `http://site.com/user/posts/5`
     * - Then extract the URI parts: `/user/posts/5`
     * - Convert the URI to list: ['user', 'posts', '5']
     * - Specify the default controller, action, params:
     *   ^ First segment `user` will be the controller.
     *   ^ Second segment `posts` will be the action.
     *   ^ Rest of segments `['5']` will be the params
     * - Run the controller: "userController", call action "posts", pass args "5"
     * - See: new userController()->posts(5);
     * ----------------------------------------------------------------------
     * - Before run the appropriate controller, action, params:
     * - Controller::load() will check if the controller exists in /controllers
     *   And will check if the target action is exists and callable, 
     *   Also will check if the provided args match the required action's args
     * ----------------------------------------------------------------------
     * - IF there is no request URI, only base_url is present: "http://site.com"
     * - Then parseUrl() will not find any URI, so no controller, action, params
     *   will be found in the request url.
     * - But we have to load a default controller when base_url requested.
     * - Therefor, constructor will assign the default controller, action 
     *   before start parsing the url.
     * - Default controller & action is defined by user in app.php
     * - i.e $config['default_controller'] = 'indexController';
     *       $config['default_action'] = 'default';
     * - in this case, router will call indexController->default() if base_url.
     * - User can set default controller from custom routes, inside routes.php
     * - i.e Route::map('/', 'homeController', 'default');
     * - IF default controller, action not defined in app.php, nether in routes.php,
     *   Router will assume the default controller is "indexController" 
     *   and will assume the default action is "default".
     */  
    private function dispatch()
    {

        $requestUri = $this->parseUrl();

        /**
         * -------------------------------------------------------------------------
         * Execute Default Middlewares
         * -------------------------------------------------------------------------
         * [1] Core Middlewares
         * - Default middlewares set by framework at src: `Core/Middlewares`
         * - example: XssMiddleware.php, CsrfMiddleware.php, etc
         * [2] User Auto-Middlewares
         * - middlewares that should always be executed, src: `config/middlewares`
         * - $autoload_middlewares = [...]
         */

        // (1) Execute Framework Middlewares
        $this->execute_framework_middlewares();
        // (2) Execute User autoload middlewares
        $this->execute_autoload_middlewares();


        /**
         * -------------------------------------------------------------------------
         * IF Base Url (Home)
         * -------------------------------------------------------------------------
         * - Check IF user mapped the home in config/routes.php, 
         * > Route::map('/', HomeController::class, 'default')
         * - In this case, do not perform default routing to avoid conflict
         * - Because user already set his own route for the home
         * -> this will avoid conflict between user routes and default routing
         */
        # Check if we are in (home)
        if($this->uri_controller == self::getDefaultController()){
            # If user has route for home, do not perform default routing.
            if(Route::is_route_exists('/')) return;
        }

        /**
         * -------------------------------------------------------------------------
         * Execute User Routes
         * -------------------------------------------------------------------------
         * - IF user has set a route for this request in `config/routes.php`
         *   Then execute that route.
         * - Otherwise, the Router will execute the default routing is enabled.
         */
        $user_route = $this->execute_user_route(strtolower($requestUri), Route::$all_routes);


        /**
         * -------------------------------------------------------------------------
         * No route? Execute Default Routing
         * -------------------------------------------------------------------------
         * - IF user does not set a route for this current request, 
         *   Then execute the default routing. 
         * - But check first if default routing is enabled by user
         */ 
        if($user_route===false) $this->execute_default_routing();

    }


    /**
     * --------------------------------------------------------------------
     *  Get Default Controller
     * ---------------------------------------------------------------------
     */
    public static function getDefaultController(){
        global $config;
        if(isset($config['default_controller']) && !empty($config['default_controller'])){
            return $config['default_controller'];
        }else{
            return self::DEFAULT_CONTROLLER;
        }
    }
     /**
     * --------------------------------------------------------------------
     *  Get Default Action
     * ---------------------------------------------------------------------
     */
    public static function getDefaultAction(){
        global $config;
        if(isset($config['default_action']) && !empty($config['default_action'])){
            return $config['default_action'];
        }else{
            return self::DEFAULT_ACTION;
        }
    }

    /**
     * Show Error
     * --------------------------------------------------------------------
     */
    private function showError(){
        if(is_devmode()){
            CodeMediator::show_error(array(
                "title" => "Wrong path",
                "content" => 'The uri '.URI::getRequestUri().' has no route, default routing is disabled',
                "level" => DANGER,
                "description" => 'You have requested a uri that does not have a pre-defined route, and the default routing is disabled, you can create a route for this uri, or enable default routing',
                "track" => ' Router{..} $this->dispatch()'
            ));
        }
        else{
            global $config;
            if(isset($config['not_found_controller'])){
                new $config['not_found_controller'];
                exit;
            }else{
               die( page_404());
            }
        }
    }

    /**
     * --------------------------------------------------------------------
     *  Execute Framework Middlewares
     * --------------------------------------------------------------------
     */
    private function execute_framework_middlewares(){
        $middlewares = glob(CORE_MIDDLEWARES.DS.'*.php'); 
        foreach ($middlewares as $file) {
            $class = pathinfo($file, PATHINFO_FILENAME);
            if(class_exists($class)){
                $mw = new $class;
                if($mw instanceof IMiddleware)$mw->handle();
            }
        }
    }

    /**
     * --------------------------------------------------------------------
     *  Execute Autoload Middlewares
     * --------------------------------------------------------------------
     * - Execute user autoload middlewares at `config/middlewares.php`.
     * - middleware can be className or alias, so check first if is an alias, 
     *   eval it with actual the className.
     * - autoload middlewares defined in: config/ autoload_middlewares[]
     * - aliases middlewares defined in: config/ middlewares_aliases[]
     */
    private function execute_autoload_middlewares(){
        global $autoload_middlewares;
        global $middlewares_aliases;
        // get middlewares
        $middlewares_list = $autoload_middlewares;

        // Check if middleware(s) is an alias
        global $middlewares_aliases;
        $aliases = array_map_assoc('strtolower', $middlewares_aliases);
        for($i=0; $i<count($middlewares_list); $i++){
            if(key_exists(strtolower($middlewares_list[$i]), $aliases)){
                // Get actual middleware class
                $middlewares_list[$i] = $aliases[strtolower($middlewares_list[$i])];
            }
        }

        // execute
        foreach($middlewares_list as $class){
            $mw = new $class;
            if($mw instanceof IMiddleware)$mw->handle();
        }
    }



    ###############################################################################

    /**
     * -----------------------------------------------------------------------------
     * Execute User Route
     * -----------------------------------------------------------------------------
     * - IF Current RequestURI has a route, execute is.
     *   (otherwise, default routing will be executed instead)
     * -----------------------------------------------------------------------------
     * @see Current URI Example : `example/posts/demo-post`
     * @see User Route Example:
     *      array(
     *           'callback' => 'callback_func',
     *           'route' => 'example/posts/{:slug}',
     *           'method' => 'POST', // GET, PATCH, ANY, etc
     *           'middlewares' => [] // 'isLogin', ExampleMiddleware::class, etc
     *      )
     * -----------------------------------------------------------------------------
     * [1] Check IF Current URI has a user route. Or return false.
     * - Lets say current request uri is: `example/posts/demo-post`
     * - Lets say user set a route to: `example/posts/{:slug}`
     * - As we can see, we have first to eval expression {:slug} before start match,
     *   otherwise (`example/posts/demo-post` == `example/posts/{:slug}`) is false.
     * 
     */
    private function execute_user_route(string $current_uri, array $user_routes=[]): ?bool{
        

        // Loop on routes: 
        # IF allow_override, default is false 
        if(Route::$allow_override) {
            // if true, last occurred route will be executed, this can be done by reverse the array
            $user_routes = array_reverse($user_routes);
        }
        foreach ($user_routes as $route) {
            
            // [1] Check IF Current URI has a user route.
            if($result = RouterUtils::is_route_match_uri($route['route'], $current_uri)){
                
                
                // [2] Route match uri, check the request method
                # IF Route has None-HTTP Methods, like MAP, VIEW, ANY, stop routing                
                if(in_array(strtoupper($route['method']), array_map('strtoupper', [Route::TYPE_MAP, Route::TYPE_VIEW, Route::TYPE_VIEW /*,Route::TYPE_AUTH*/]))){
                    // exit instead of return
                    exit; 
                    //return true;
                }

                # IF route has HTTP Method, check if match the URI Request method
                if(strtolower($_SERVER['REQUEST_METHOD']) != strtolower($route['method'])){
                    continue;
                }

                // [3] Check if route has middlewares
                if(!empty($route['middlewares'])){
                    // if middlewares found execute them.
                    // if one of them not passed, the function will exit the program
                    $this->execute_route_middlewares($route);
                }

                // [4] Execute route callback
                if(isset($route['callback'])){

                    if($result->params!=null && !empty($result->params)){
                        // PHP-7 call_user_func_array($route['callback'], $result->params);
                        // PHP-8 Keys in call_user_func_array args are not supported
                        // So we have to use array_values() to remove the keys
                        call_user_func_array($route['callback'], array_values($result->params));
                    }else{
                        call_user_func($route['callback']);
                    }
                }

                # true: do not execute default routing
                return true; 

            }
        }

        // No route found, return false
        # this will execute default routing
        return false; 
        

    }

   

    /**
     * -------------------------------------------------------------------------
     * Execute Default Routing
     * -------------------------------------------------------------------------
     * - When user does not set a route for current uri/request, 
     *   Then execute default routing if is enabled.
     * - Example: if request is: /auth/login/
     *  Then Controller::load() will call AuthController{}->login()
     */
    private function execute_default_routing(){
        // Check if default routing is enabled
        global $config;
        if($config['enable_default_routing']){
            // do routing
            Controller::load($this->uri_controller, $this->uri_action, $this->uri_params);
        }
        else{
            // default routing disabled, show error
            $this->showError();
        }
    }

    /**
     * ------------------------------------------------------------------------
     * Execute Route Middlewares
     * ------------------------------------------------------------------------
     * - Route may contains one or list of middlewares.
     * - IF one middleware has not passed, execute callback if found, 
     *   Then exit the program.
     * - IF fallback has arg, we can pass which middleware fall as arg.
     * - Route Must match the request URI
     * ------------------------------------------------------------------------
     * @see Route structure:
     *  array(
     *      'callback' => 'callback_func',
     *      'route' => 'example/posts/{:slug}',
     *      'method' => 'POST', // GET, PATCH, ANY, etc
     *      'middlewares' => [
     *        array(
     *            'class' => null, // ExampleMiddleware::class,
     *            'fallback' => null, // function(){..}
     *        )
     *     ] 
     *  )
     * ------------------------------------------------------------------------
     * @return boolean
     * - Return true if all middlewares has been passed.
     */
    private function execute_route_middlewares(?array $route){
        // middleware will execute the callback if success
        foreach($route['middlewares'] as $middleware){
            $fallback = isset($middleware['fallback']) ? $middleware['fallback'] : null;
            RouterUtils::execute_middleware($middleware['class'], $fallback);
        }
    }

}
