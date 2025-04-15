<?php 

namespace Core\Interfaces;

use Core\Classes\Route;

/**
 * @warning
 * - IF more than one route with same function is used, 
 *   the first route will be implemented.
 * @example (2)
 *   Route::view('api/auth', 'register');
 *   Route::view('api/auth', 'login');
 * - In this case, only first route will be evaluated,
 *   So router will load view 'register' only.
 * @example (2)
 *   Route::get("api/home/", function(){
 *     echo "first";
 *   })
 *   Route::get("api/home/", function(){
 *     echo "second";
 *   })
 * - In this case, only the first route will be evaluated, 
 *   So code will output "first" only;
 * 

 */
interface IRouter{

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
     * [-] Route Parameters |
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
    public static function get(string $uri, callable $callback): Route;


    /**
     * =======================================================================
     * POST
     * =======================================================================
     * Used to create new resource 
     */ 
    public static function post   (string $uri, callable $callback): Route;

    /**
     * =======================================================================
     * PUT: replace entire columns
     * =======================================================================
     * PUT is used to replace the entire resource with a new version.
     * If you use PUT to update multiple columns, 
     * you would need to send the entire resource with all the columns, 
     * including the ones that haven't changed.
     */
    public static function put    (string $uri, callable $callback): Route;
    /**
     * =======================================================================
     * PATCH: apply partial updates
     * =======================================================================
     * PATCH is used to apply partial updates to a resource.
     * When using PATCH to update multiple columns, 
     * you only need to send the changes, 
     * example: the columns that need to be updated. (status, role, etc)
     */
    public static function patch  (string $uri, callable $callback): Route;
    public static function delete (string $uri, callable $callback): Route;
    public static function options(string $uri, callable $callback): Route;
    public static function head(string $uri, callable $callback): Route;



    /**
     * =========================================================================
     *   Route Any
     * =========================================================================
     * - Create a route that respond to any http request: get, post, put, etc.
     * - a callable is used to execute such code when route is matches the uri.
     * -----------------------------------------------------------------------
     * @example 
     *   Route::any('/home', function(){
     *       echo "The htp request is: ".$_SERVER['HTTP_REQUEST'];
     *   });
     * 
     */
    public static function any(string $uri, callable $callback): Route;

    /**
     * =========================================================================
     *   Map Route To Controller
     * =========================================================================
     * - Map specific route to a controller 
     * - There is now need to use callback
     * -----------------------------------------------------------------------
     *
     * @example Map route to controller without action
     *    Route::map('home', homeController::class);
     * 
     * @example Map route to controller with action
     *    Route::map('home', homeController::class, "sayHello");
     * 
     * @example Map route to controller with action & single param
     *    Route::map('home', homeController::class, "sayHello", "arg1");
     * 
     * @example Map route to controller with action & multi params
     *    Route::map('home', homeController::class, "sayHello", ["arg1", "arg2"]);
     * 
     * @example Use with callback
     *   Route::map('home', homeController::class, "sayHello", function(){
     *     // map performed
     *   });
     * 
     */
    public static function map(string $route, $className, $actionName=null, $provided_params=null, $callback=null): Route;

    /**
     * =======================================================================
     *   Redirect
     * =======================================================================
     * - redirect uri to new uri, no need to pass base url
     * - The method return code_301 by default. you can set different like: 302
     * @example(
     * - Simple Redirect
     *    Route::redirect('api/v1', 'api/v2');
     *    Route::redirect('/index', '/home');
     *    Route::redirect('/index', '/home', 301); // 301: page moved temporary
     * @example 
     * - Redirect, but hold some parts from old route to new route using :any
     * - Suppose we have this url: "site.com/api/v1/posts/3/comments/10"
     *   And we only want to change `v1` to `v2` and keep the rest of url?
     *   This can be done using (:any) which will hold the rest
     *   Route::redirect('api/v1/(:any)', 'api/v2/(:any)');
     * - The result would be: "site.com/api/v2/posts/3/comments/10"
     * 
     * @example 
     * - :any can be used in both old and new route, or in only old route
     * - lets say we want to redirect anything after v1 to home
     * - Route::redirect('api/v1/(:any)', 'home');
     * 
     */ 
    public static function redirect(string $uri, string $new_uri, int $code=302);



    /**
     * =======================================================================
     *   View Routes
     * =======================================================================
     * - IF we only needs to load a view (static), 
     *   we can use this method to show the corresponded view.
     * - So that we don't need to define a full route or controller.
     * @example 
     *   Route::view('/login', 'login'); // `login` defined in views/login.php
     * - We may want to pass some data to the view, 
     *   So that we can create array that wrap the data needs to be passed.
     * @example
     *   Route::view('/login', 'login', ['name' => 'Sam']);
     * 
     * @warning 
     * > The view must not be depends of a controller.
     * > this function load view directly.
     * > if the view depends on a controller, like it get data from controller
     *   then do not use this method.
     */
    public static function view(string $uri, string $view, array $data=null): Route;


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
     */
    public static function middleware(string $className, $fallback=null);
}