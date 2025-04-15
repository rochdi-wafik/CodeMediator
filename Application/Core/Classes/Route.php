<?php
namespace Core\Classes;

use Core\Interfaces\IRouter;
use Core\RouterUtils;

/*
 * ******************************************************************************
 * CodeMediator - Route
 * ******************************************************************************
 *
 * Do Not Edit Or Remove Anything From This File. 
 *
 * ******************************************************************************
 * - Some Routes methods are executed by Router,php, some executed by Route.php
 * - [Router.php]
 * - These methods accept callbacks and optionally accept middlewares.
 *   These methods are stored by this class to be executed by Router.php.
 *   They usually handle HTTP Request like: GET-POST-PUT-etc 
 * - get(), post(), put(), patch(), delete(), head(), options()
 * - [Route.php]
 * - These methods does not accept callbacks or middlewares. 
 *   are they are executed directly by this class. Example:
 * - map(), any(), view(), redirect()

 *      
 */
class Route implements IRouter{
    private static $instance = null;
    public static $allow_override = false; 

    public const ANY = "(:any)";
    public const TYPE_MAP = "MAP";
    public const TYPE_ANY = "ANY";
    public const TYPE_VIEW = "VIEW";
    public const TYPE_AUTH = "AUTH";

    public static $all_routes = [
        // Route stack example
        // array(
        //     'callback' => 'callback_func',
        //     'route' => 'example/posts/{:slug}',
        //     'method' => 'POST', // GET, PATCH, ANY, etc
        //     'middlewares' => [
        //         array(
        //             'class' => null, // ExampleMiddleware::class,
        //             'fallback' => null, // function(){..}
        //         )
        //     ] 
        // ),
    ];



    // Store the custom routes 
    public static $routes_auth = [];



    /**
     * =====================================================================
     *   Static Init
     * =====================================================================
     * - This method must be called firstly before call any other methods
     * - This method set some configs that has to be done
     *    before start using routes
     * @see
     * - This method makes sure to run parent controller if founds
     *   before run any other controller.
     * --------------------------------------------------------------------
     * @param allow_override 
     * - IF we have two or more routes matched the same current URI, 
     *   Do we override previous routes and execute the last route?
     * - Default is false, means first occurred route will be executed.
     *   any rest will be ignored.
     *  
     */
    public static function init(bool $allow_override=false){
        self::$allow_override = $allow_override;
        
        // [-] Run Parent Controller
        // - Before load any controller, load Parent Controller if exists
        global $config; 
        if(isset($config['parentController']) && !empty($config['parentController'])){
            $parentController = $config['parentController'];
            new $parentController;
        }
    }

    
    /**
     * =====================================================================
     * Get Context
     * =====================================================================
     * - Instead of return new self() each time we need to return self, 
     *   We could use a method that do that for us.
     * - Also, later we may need to apply actions before we return the class,
     *   We could do that from one method instead of update all methods.
     */
    public static function getContext() : Route{
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    ##############################################################################
    ##############################################################################
    ##############################################################################

    /**
     * =======================================================================
     *   Basic Routing
     * =======================================================================
     * - Route simple url using get request. 
     * - a callable is used to execute such code when route is matches url.
     * -----------------------------------------------------------------------
     * @example print welcome 
     *   Route::get('/home', function(){
     *       echo "Welcome Home";
     *   });
     * -----------------------------------------------------------------------
     * @example load home controller with method welcome
     *  Route::get('home', function(){
     *     new homeController()->welcome();
     *  })
     * -----------------------------------------------------------------------
     * @example load view `home` and pass data
     *   Route::('home', function(){ 
     *       view('welcome', ['txt' => 'Hello user!']);
     *   })
     * -----------------------------------------------------------------------
     * [-] Route Parameters {__expr__}
     * -----------------------------------------------------------------------
     * - We may need to capture segments of our uri.
     * - Example: we may need to capture a userID from the uri.
     * @example
     *   Route::get('/user/{id}', function (string $id) {
     *       echo 'User id is: '.$id;
     *   });
     * - we can define as many route parameters as we need:
     * - we don't have to name to uri_param name exactly like the param_name.
     * - Example: {postID} => $postID  OR  {post} => $postID etc
     * @example
     *   Route::get('/posts/{post}/comments/{comment}', function ($postId,$commentId){
     *      // ...
     *   });
     * 
     * -----------------------------------------------------------------------
     * [-] Route Parameters (:any)
     * -----------------------------------------------------------------------
     * - we may need to catch rest of part of url. 
     * - Lets say we have this url: "site.com/api/v1/posts/1/comments/8"
     * - Suppose we want to catch the rest after v1, then we would use :any:
     * @example
     *   Route::get('api/v1/(:any)', function($any){
     *     // $any = ['posts', '1', 'comments', '8]
     *     // we can implode $any to get: "/posts/1/comments/8"
     *   });
     * -----------------------------------------------------------------------
     * [-] Optional Parameters |
     * -----------------------------------------------------------------------
     * - we may need to specify a route parameter that may not always be exists in the URI. 
     * - we can do so by placing a ? mark after the parameter name. 
     * - We have to give the callback variable a default value
     *   in case value not exists in the uri.
     * @example
     *  Route::get('/user/{name?}', function (?string $name = null) {
     *       if($name==null) //...
     *   });
     * 
     * @example   
     *   Route::get('/user/{name?}', function (?string $name = 'John') {
     *       //... 
     *   });
     *      
     */

    /**
     * =======================================================================
     *   GET Request
     * =======================================================================
     * [1] Check if REQUEST METHOD == GET
     * [2] Check if route not already set (is new) @todo we may need to override routes
     * [3] Check if route match URL ? execute the callback and params
     * [4] Store route to prevent duplication (to be checked by step 1)
     * =======================================================================
     * @example
     * Route::get('api/v1/posts', function(){...});
     * Route::get('api/v1/posts/{:id}', function($id){...});
     * Route::get('api/v1/posts/{:id}/comments', function($id){...});
     * Route::get('api/v1/posts/{:id}/users/{:id}', function($post_id, $user_id){...});
     */ 
    public static function get(string $route, callable $callback): Route{
        self::save_route($route, $callback, 'GET');
        return self::getContext();
    }

    /**
     * =======================================================================
     *   POST Request
     * =======================================================================
     * 
     */ 
    public static function post   (string $route, callable $callback): Route{
        self::save_route($route, $callback, 'POST');
        return self::getContext();
    }

    /**
     * =======================================================================
     *   PUT Request
     * =======================================================================
     * 
     */ 
    public static function put(string $route, callable $callback): Route{
        self::save_route($route, $callback, 'PUT');
        return self::getContext();
    }

    /**
     * =======================================================================
     *   HEAD Request
     * =======================================================================
     * 
     */ 
    public static function head(string $route, callable $callback): Route{
        self::save_route($route, $callback, 'HEAD');
        return self::getContext();
    }

    /**
     * =======================================================================
     *   PATCH Request
     * =======================================================================
     * 
     */ 
    public static function patch  (string $route, callable $callback): Route{
        self::save_route($route, $callback, 'PATCH');
        return self::getContext();
    }

    /**
     * =======================================================================
     *   DELETE Request
     * =======================================================================
     * 
     */ 
    public static function delete (string $route, callable $callback): Route{
        self::save_route($route, $callback, 'DELETE');
        return self::getContext();
    }

    /**
     * =======================================================================
     *   OPTIONS Request
     * =======================================================================
     * 
     */ 
    public static function options(string $route, callable $callback): Route{
        self::save_route($route, $callback, 'OPTIONS');
        return self::getContext();
    }


    ##############################################################################
    ##############################################################################
    ##############################################################################

    
    /**
     * =========================================================================
     *   Route Any HTTP Method
     * =========================================================================
     * - Route any HTTP Method like GET, POST, PUT, PATCH, etc.
     * - A callable is used to executed when route matches the current URI.
     * - This method support patterns like (:any) {:id} {:name} etc
     * -----------------------------------------------------------------------
     * @example 
     *   Route::any('/home', function(){
     *       echo "The htp request is: ".$_SERVER['REQUEST_METHOD'];
     *   });
     *   Route::any('/post/{:name}', function($name){
     *       echo "Post name: ".$name;
     *   });
     *   Route::any('/api/v1/(:any)', function($data){
     *       print_r($data);
     *   });
     */
    public static function any(string $route, callable $callback): Route{
        // If route already exists, return.
        if(self::is_route_exists($route, "ANY")) {
            if(self::$allow_override===false) return self::getContext();
        }

        // Check if route match url
        $result = RouterUtils::is_route_match_uri($route);

        if(is_object($result)){
            // Save this route to avoid conflict/duplication
            self::save_route($route, $callback, "ANY");

            // Execute the Callback
            $callback_params = $result->params; 
            if($callback_params!=null && !empty($callback_params)){
                // evaluated patterns as params
                call_user_func_array($callback, $callback_params);
            }else{
                // no params
                call_user_func($callback);
            }
            
        }

        return self::getContext();
    }

    /**
     * =========================================================================
     *   Map Route To Controller
     * =========================================================================
     * - Map specific route to a specific controller .
     * - This method does not support patterns for now like {:id}
     * - This method does not support middlewares for now.
     * -----------------------------------------------------------------------
     *
     * @example Load without action
     *    Route::map('home', homeController::class);
     * 
     * @example Load With action
     *    Route::map('home', homeController::class, "sayHello");
     * 
     * @example Load With action & single param
     *    Route::map('home', homeController::class, "sayHello", "arg1");
     * 
     * @example Load With action & multi params
     *    Route::map('home', homeController::class, "sayHello", ["arg1", "arg2"]);
     * 
     */
    public static function map(string $route, $className, $actionName='default', $provided_params=null, $callback=null): Route{
        // If route already exists, return.
        if(self::is_route_exists($route, "MAP")) {
            if(self::$allow_override===false) return self::getContext();
        }

 

        // Check if route match url
        $result = RouterUtils::is_route_match_uri($route);

        if(is_object($result)){
            // Save this route to avoid conflict/duplication
            self::save_route($route, $callback, "MAP");
            
            // Load target controller
            Controller::load($className, $actionName, $provided_params);
            // Invoke callback
            if($callback!=null && is_callable($callback)) call_user_func($callback);
        }

        return self::getContext();
    }

    /**
     * =======================================================================
     *   Route View
     * =======================================================================
     * - Load view when route match the url.
     * - Pass params (if found) to the view.
     * -----------------------------------------------------------------------
     * @warning 
     * - The target view must not be depends on a controller.
     * - i.e if the view get data from controller using View::getData('foo'),
     *   then do not use this method, because this method not responsible of
     *   create the controller of the view.
     * @example
     * - Assume we have a view named login at `Application/Views/login.php`
     *   And we wan't to load that view when user request /auth
     * $  Route::view('auth', 'login');
     * 
     */ 

    public static function view(string $route, string $view, array $data=null): Route{
        // If route already exists, return.
        if(self::is_route_exists($route, "VIEW")) {
            if(self::$allow_override===false) return self::getContext();
        }

        // Check if route match the current URI
        $result = RouterUtils::is_route_match_uri(strtolower($route));

        if(is_object($result)){
            // Save this route to avoid conflict/duplication
            self::save_route($route, null, "VIEW");
            // Show target View
            View::show($view, $data);
        }

        return self::getContext();
    }

    /**
     * =======================================================================
     *   Redirect
     * =======================================================================
     * - redirect uri to new uri, no need to pass base url
     * - The method return code_301 by default. you can set different like: 302
     * @example
     * - Simple Redirect
     *    Route::redirect('api/v1', 'api/v2');
     *    Route::redirect('/index', '/home');
     *    Route::redirect('/index', '/home', 301); // 301: page moved temporary
     * -------------------------------------------------------------------------
     *            Using (:any)
     * -------------------------------------------------------------------------
     * - Assume we have this URL: `site.com/api/v1/posts/3/comments/10`
     *   And we only want to change `v1` to `v2` and keeping the rest of url?
     * - This can be done using (:any) which will hold the rest of uri after it.
     * @example 
     * $ Route::redirect('api/v1/(:any)', 'api/v2/(:any)');
     * - The new URL result will be: `site.com/api/v2/posts/3/comments/10`
     * - (:any) can be used in both old and new route, or in only old route
     * - Lets say we want to redirect anything after v1 to home
     * - Route::redirect('api/v1/(:any)', 'home');
     * 
     */ 

    public static function redirect(string $old_route, string $new_route, int $code=302){
        $url = RouterUtils::get_current_uri();

        // loop on route, if we found :any, count before any if match url
        // Split uri and route to parts
        $uri_parts =  array_map('strtolower', explode('/', trim($url, '/')));
        $old_route_parts = array_map('strtolower', explode('/', trim($old_route, '/')));
        $new_route_parts = array_map('strtolower', explode('/', trim($new_route, '/')));
       
        // Old route before :any
        $old_routes_before_any = [];
        foreach($old_route_parts as $i => $part){
            if($part !== self::ANY){
                $old_routes_before_any[$i] = $part;
            }
            else{
                break;
            }
        }

        // Get the number of items in the $route_parts_before_any array
        $count = count($old_routes_before_any);

        // Slice the $uri_parts array to get the items that still match
        $uri_before_any = array_slice($uri_parts, 0, $count);

       // Slice the $uri_parts array to get the rest of the items
        $uri_after_any = array_slice($uri_parts, $count);

        // check if uri match old route
        if(array_diff($uri_before_any, $old_routes_before_any)){
            // not match
            return;
        }

        // New route evaluated ( replace :any with value if found)
        $new_routes_eval = [];
        foreach($new_route_parts as $i => $part){
            if($part !== self::ANY){
                $new_routes_eval[$i] = $part;
            }
            else{
                // if there is :any, replace it with uri_after_any
                $new_routes_eval[$i] = implode('/', $uri_after_any);
            }
        }

        // Generate new uri
        $new_uri = implode('/', $new_routes_eval);

        // Perform redirect
        $new_url = base_url($new_uri);
        header("Location: $new_url", true, $code);
        exit;
    }

    /**
     * =======================================================================
     *   Route Auth
     * =======================================================================
     * - Allow access to specific route only if client is Authorized.
     * - This can be done by assign a MiddleWare directly.
     * - This method allow patterns like (:any) {:id} {:name} etc.
     * -----------------------------------------------------------------------
     * @example
     * - Only allow access to user pages if client has role = user (and login)
     *   Route::auth('user/(:any)', isUserMiddleware::class); 
     * - With fallback 
     *   Route::auth('user/(:any)', isUserMiddleware::class, function(){
     *         die('Access denied!');
     *   }); 
     * -----------------------------------------------------------------------
     * @param route i.e 'user/(:any)', 'api/v1/user', etc
     * @param middleware can be single middleware or a list of middlewares
     *        i.e `IsLoginMiddleware::class` OR as alias `isLogin`
     * @param fallback will be invoked if middleware(s) not passed (fall)
     *        if no fallback, this will return UNAUTHORIZED.
     */ 

    public static function auth(string $route, $middleware, callable $fallback=null): void{
        // If route already exists, return.
        if(self::is_route_exists($route, "AUTH")) {
            if(self::$allow_override===false) return;
        }

        // Check if route match the current URI
        $result = RouterUtils::is_route_match_uri(strtolower($route));

        if(is_object($result)){
            // Save this route to avoid conflict/duplication
            self::save_auth_route($route, null, "AUTH");

            // Execute middleware
            // Ff not passed, fallback will be invoked
            // IF no fallback, app will return UNAUTHORIZED
            RouterUtils::execute_middleware($middleware, $fallback);
        }
    }

    ##############################################################################
    ##############################################################################
    ##############################################################################


    /*###########################[ MiddleWare ]#############################*/

    /**
     * =======================================================================
     * MiddleWare
     * =======================================================================
     * @param className can be Middleware class name or alias of it
     * We first check: if its not a class name, find in aliases.
     * className can also be a list of middlewares.
     * @param fallback (optional) it will be invoked if middleware fall. 
     * 
     * @example
     * $  Router::get()->middleware(AuthMiddleware::class);
     * $  Router::get()->middleware(AuthMiddleware::class, function(){..});
     * $  Router::get()->middleware([First::class, Second::class]);
     * 
     * - Using aliases
     * $  Router::get()->middleware('isLogin');
     * $  Router::get()->middleware('isLogin', function(){..});
     * $  Router::get()->middleware(['isAdmin', 'isLogin']);
     * =======================================================================
     * - WE have to bind the middleware with the target route.
     * - Each route should has an empty array of middlewares.
     * - We have to add this middleware to the array in the route.
     * - The Router will check if the route has a middleware(s) in the array.
     * @see
     * $route = [
     *    'uri' => '/example/posts/23',
     *    'route' => 'example/posts/{:id}',
     *    'method' => 'GET',
     *    'middleware' => []
     * ];
     */
    public static function middleware($middleware, $fallback=null){
        // convert to array: if item, add to array, if array, merge with
        $middlewares_list = [];
        if(is_array($middleware)){
            $middlewares_list = array_merge($middlewares_list, $middleware);
        }elseif(is_string($middleware)){
            array_push($middlewares_list, $middleware);
        }else{
            return;
        }

        // Check if middleware(s) is an alias
        global $middlewares_aliases;
        $aliases = array_map_assoc('strtolower', $middlewares_aliases);
        for($i=0; $i<count($middlewares_list); $i++){
            if(key_exists(strtolower($middlewares_list[$i]), $aliases)){
                // Get actual middleware class
                $middlewares_list[$i] = $aliases[strtolower($middlewares_list[$i])];
            }
        }

        // Save the middleware to the appropriate route
        self::save_middleware($middlewares_list, $fallback);
    }
    


    #####################################################################
    ################################# Helpers ###########################
    #####################################################################

    /**
     * -----------------------------------------------------------------
     * Is Route Exists
     * -----------------------------------------------------------------
     * - Check if route already exists in the routes list.
     * @param method Example: GET, POST, ANY, etc
     * @param route  Example: 'api/v1/posts/{:id}'
     */
    public static function is_route_exists(string $route, string $method=null): ?bool{
        $items = array_filter(self::$all_routes, function($item) use ($route, $method) {
            if($method!=null){
                return strtolower($item['route']) === strtolower($route) 
                && strtolower($item['method']) === strtolower($method);
            }else{
                return strtolower($item['route']) === strtolower($route);
            }
        });

        return empty($items) ? false : true;
    }

    public static function is_auth_route_exists(string $route): ?bool{
        $items = array_filter(self::$routes_auth, function($item) use ($route) {
            return strtolower($item['route']) === strtolower($route) 
            && strtolower($item['method']) === strtolower("AUTH");
        });

        return empty($items) ? false : true;
    }


    /**
     * -----------------------------------------------------------------
     * Save Route
     * -----------------------------------------------------------------
     * - This method will help us store the route in routes list.
     * - Only store the route if not stored.
     * 
     * @param requestUri Example: `example/posts/demo-post`
     * @param route Example: `example/posts/{:slug}`
     * @method example: GET, POST, PUT, ANY, etc
     */
    private static function save_route(string $route, callable $callback=null, $method='GET'): void{
        // Check if route already exists
        if(self::is_route_exists($route, $method)){
            // if override disabled, return
            if(self::$allow_override===false) return;
        }

        // Save the route
        array_push(self::$all_routes, array(
            'route' => $route, 
            'callback' => $callback,
            'method' => $method,
            'middlewares' => []
        ));
    }

    private static function save_auth_route(string $route, callable $callback=null): void{
        // Check if route already exists
        if(self::is_auth_route_exists($route)){
            // if override disabled, return
            if(self::$allow_override===false) return;
        }

        // Save the route
        array_push(self::$routes_auth, array(
            'route' => $route, 
            'callback' => $callback,
            'method' => "AUTH",
            'middlewares' => []
        ));
    }

    /**
     * -----------------------------------------------------------------
     * Save Middleware
     * -----------------------------------------------------------------
     * - Middleware will be added to the last route, why?
     * - Because middleware() called right after Route::function. 
     * - Middleware can be an alias, so check if is alias, 
     *   then save the actual middleware
     */
    private static function save_middleware(array $middlewares, callable $fallback=null){
        $middleware_arr = [];
        foreach($middlewares as $middleware){
            array_push($middleware_arr, array(
                'class' => $middleware, 'fallback' => $fallback
            )); 
        }

        // (1) Get last route from the list
        $last_route = end(self::$all_routes);
        // (2) Get key middlewares
        $middlewares_list =  $last_route['middlewares'];
        // (3) Merge given middleware to the route middlewares
        $middlewares_list = array_merge($middlewares_list, $middleware_arr);
        $last_route['middlewares'] =  $middlewares_list;
        // (4) Update last route in the routes list
        $last_route_index = array_last_index(self::$all_routes);
        self::$all_routes[$last_route_index] = $last_route;   
    }




   



}

