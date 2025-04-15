<?php 
namespace Core;

use Core\Classes\Route;
use Core\Interfaces\IMiddleware;
use Exception;
use stdClass;

/**
 * ***************************************************************************
 * Router Utils
 * ***************************************************************************
 * - This class contains some functions needed by Router.
 * - Instead of put everything in Router.php, we put utils in this class.
 */
class RouterUtils{
        /**
     * ======================================================================
     *  Check Is Route Expression: {__expr__}
     * ======================================================================
     * - if route contain expression.
     * @example {:userID}, {postID}, {anything}
     */
    public static function isExpression(string $item){
        // OLD {expr}
        // if (preg_match('/^{([a-zA-Z0-9_]+)}$/', $item, $match)){
        //     return true;
        // }

        // NEW {:expr} OR {expr}
        if (preg_match('/^{(:?[a-zA-Z0-9_]+)}$/', $item, $match)) {
            return true;
        }
        return false;
    }

    /**
     * ======================================================================
     *  Check Is Route Nullable Expression: {__expr__?}
     * ======================================================================
     * - if route contain expression,
     *   but this expression may exists or not exists in the url.
     * @example {userID?}, {postID?}, {anything?}
     */
    public static function isNullableExpression(string $item){

        if (preg_match('/^{([a-zA-Z0-9]+)\?}$/', $item, $match)){
            // return expression value from the current uri (eval route/uri)
            //return $match[0];
            return true;
        }
        return false;
    }

    /**
     * -----------------------------------------------------------------------------
     * Check IF URI Match Route
     * -----------------------------------------------------------------------------
     * - because route may contain expressions like {__EXPR__}, 
     *   we need to eval this expression before compare route with uri.
     * -----------------------------------------------------------------------------
     * @see example
     *   Route = "api/posts/{postID}/comments/{commentID}";
     *   URI   = "api/posts/23/comments/10";
     * - as we can see, our rout match the uri, but we have to eval the expressions
     *   {postID} and {commentID} to there actual value from uri. then we can compare.
     * -----------------------------------------------------------------------------
     * @see (:any)
     * - If route contains (:any), this means take everything after :any, example:
     * + URI => "api/v1/posts/5/comments/10"
     * + ROUTE => "api/v1/(:any):
     * + (:any) => "posts/5/comments/10"
     * - IF route contains (:any), then eval :any with the url
     * -----------------------------------------------------------------------------
     * @return mixed object id match or false.
     * - IF Uri match the route, the function will return object contains:
     * (1) evaluated route, output: $obj->route =  "api/posts/23/comments/10"
     * (2) list of expressions, output: $obj->params = ['postID'=>23, 'commentID' => 10]
     * 
     * -------------------------------------------------------------------------
     * @todo support Nullable expressions.
     * @example get(`blog/post/{?:slug}`, function(){..});
     * {?:slug} means that the uri may or may not have a slug after `blog/post`
     * -------------------------------------------------------------------------
     * @todo support nullable (:any)
     * @example auth('user/(:?any)', 'isLogin');
     * - (:?any) means that the uri may or may not have parts after user/ 
     * - specifically: apply auth if uri = 'user' or if user = 'user/something'
     * - using (:any) instead of (:any), we'll have to make route for 'user' and
     *   for user/something.
     * [Before]
     *   Route::auth('user', 'isLogin');
     *   Route::auth('user/(:any)', 'isLogin');
     * [After]
     *   Route::('user/(:?any)', 'isLogin');
     */
    public static function is_route_match_uri(string $route, $uri=null): ?stdClass{
        // default URI = current URI
        if($uri==null) $uri = self::get_current_uri();

        // Split uri and route into parts
        $uri_parts =  array_map('strtolower', explode('/', trim($uri, '/')));
        $route_parts = array_map('strtolower', explode('/', trim($route, '/')));
        $eval_route_parts = [];
        $params_list =[];

        /**
         * IF route contain expression (:any)
         * -----------------------------------------------------------------
         * - Then any will be evaluated to the rest of URI, example:
         * + URI => "site.com/api/v1/posts/5/comments/10"
         * + Route => "api/v1/(:any)
         * + (:any) => [posts/5/comments/10]
         */
        if(in_array(Route::ANY, $route_parts)){
            if(count($uri_parts) >= count($route_parts)){
                foreach($route_parts as $i => $part){
                    if($part !== Route::ANY){
                        $eval_route_parts[$i] = $part;
                    }else{
                        break;  // $eval_route_parts = ['api','v1']
                    }
                }
                // uri before :any => cut uri from 0 until where :any found in route
                $uri_before_any = array_slice($uri_parts, 0, count($eval_route_parts));
                // uri after :any => cut uri from where :any found to the rest
                $uri_after_any = array_slice($uri_parts, count($eval_route_parts));
    
                // eval :any in the route: add uri_after_any to route
                $eval_route_parts = array_merge($eval_route_parts, $uri_after_any);
    
                // store :any parts to be passed as arg to callback
                // call_user_func_array will pass each item in $params_list as new argument
                // to the callback, but we want to pass the whole array as one argument type array
                // because  we have :any, user should get array of items of :any.
                // so that we use $params_list = array($uri_after_any);  instead of $params_list = $uri_after_any
                $params_list = array($uri_after_any); 
            }
            
        }

        /**
         * IF route contains dynamic expression
         * ------------------------------------------------------------------------
         * - Dynamic expr means any expr set by user, like {:id} {:slug} etc, i.e:
         * + URI => "site.com/api/v1/posts/5/comments/10"
         * + Route => "api/v1/posts/{postID}/comments/{commentID}"
         * + expressions => ['postID' => 5,'commentID' => 10]
         */
        else{   
            // Check if Current URI match the specified Route
            if(count($uri_parts) === count($route_parts)){ //@todo problem when nullable (?) count will give false
                // Check if route contain expressions {expr}
                foreach($route_parts as $i => $route_part){
                    if(self::isExpression($route_part)){
                        $eval_route_parts[$i] = $uri_parts[$i];
                        $params_list[$route_part] = $uri_parts[$i];
                    }else{
                        $eval_route_parts[$i] = $route_part;
                    }
                }
            }

        }

        /**
         * Return result
         * ---------------------------------------------------------------
         * - IF Uri NOT match route, false;
         * - Otherwise: return object contains details,
         * - any user expressions like {:id} {:slug} will be a callback params.
         */

        // IF Uri not match route:
        if(!arrays_equal($uri_parts, $eval_route_parts)) return null;

        // IF Uri match route:
        $resultObj = new stdClass;
        $resultObj->uri_parts = $uri_parts ;
        $resultObj->route_parts = $eval_route_parts;
        $resultObj->eval_route = implode('/', $eval_route_parts);
        $resultObj->params =(!empty($params_list)) ? $params_list : null;
        return $resultObj;

    }

    
    /**
     * ------------------------------------------------------------------------
     * Execute Middleware(s)
     * ------------------------------------------------------------------------
     * - Execute a middleware or a list of middlewares.
     * - Middleware can be class name or alias.
     * ------------------------------------------------------------------------
     * @param middlewares can be single middleware or a list of middlewares
     *        i.e `IsLoginMiddleware::class` OR as alias `isLogin`
     * @param fallback will be invoked if middleware(s) not passed (fall)
     *        if no fallback, this will return UNAUTHORIZED.
     */
    public static function execute_middleware($middlewares, callable $fallback=null){
        // if single, convert to array:
        $middlewares = (array) $middlewares;

        // Check if middleware(s) is an alias, convert to actual class name
        // Example, from array('isLogin') to array(isLoginMiddleware::class)
        global $middlewares_aliases;
        $aliases = array_map_assoc('strtolower', $middlewares_aliases);
        for($i=0; $i<count($middlewares); $i++){
            if(key_exists(strtolower($middlewares[$i]), $aliases)){
                // Get actual middleware class
                $middlewares[$i] = $aliases[strtolower($middlewares[$i])];
            }
        }

        // Execute middleware, if one fall, execute fallback
        // IF no fallback set, return UNAUTHORIZED
        foreach($middlewares as $middleware){
            // If middleware class exists
            if(class_exists($middleware)){
                // instantiate the middleware
                $middlewareObj = new $middleware;
                // Is method handle() exists
                if($middlewareObj instanceof IMiddleware && method_exists($middlewareObj, 'handle')){
                    // if middleware not passed
                    if(!($isPassed = $middlewareObj->handle())){
                        // execute fallback if isset
                        if($fallback!=null && is_callable($fallback)){
                            try{
                                // execute with arg (arg=middleware name)
                                call_user_func_array($fallback, [$middleware]);
                            }
                            catch(Exception $e){
                                // no arg provided? execute without arg
                                call_user_func($fallback);
                            }
                        }
                        // IF no fallback, return UNAUTHORIZED
                        else{
                            header("HTTP/1.1 401 Unauthorized"); 
                            header('X-Error-Message: You are unauthorized to access this page!');
                            echo "401 Unauthorized";
                        }
                        // Exit the program
                        exit;
                        
                    }
                }
                else{
                    // Handle() not implemented
                    if(is_devmode()){
                        CodeMediator::show_error([
                            'title' => "Middleware warning",
                            'content' => "middleware $middleware does not implement the function handle()"
                        ]);
                    }else{
                        header("HTTP/1.1 401 Unauthorized"); 
                        header('X-Error-Message: You are unauthorized to access this page!');
                        echo "401 Unauthorized";
                        exit;
                    }
                }
            }
            else{
                // Middleware class not found 
                if(is_devmode()){
                    CodeMediator::show_error([
                        'title' => "Middleware warning",
                        'content' => "middleware $middleware cannot be found or instantiated"
                    ]);
                }
                else{
                    header("HTTP/1.1 401 Unauthorized"); 
                    header('X-Error-Message: You are unauthorized to access this page!');
                    echo "401 Unauthorized";
                    exit;
                }
            }
        }
    }

        /**
     * =====================================================================
     *   Get Current Url
     * =====================================================================
     */
    public static function get_current_uri(){
        $url = URI::getRequestUri();
        $url = strtolower($url);

        // decode url in case has non-latin chars like arabic
        if(verify_config('url_decode', true))
        {
            $url = urldecode($url); 
        }
        
        // parse url
        $uri = parse_url($url,PHP_URL_PATH); // filter queries (?q=..)
        // if($uri=="" || empty($uri) || $uri==null){
        //     $uri = 'index';
        // }
        // echo "Is uri empty? ". ($uri=="" || empty($uri) || $uri==null)."<br>";
        
        return $uri;
    }

}