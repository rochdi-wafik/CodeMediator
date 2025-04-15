<?php 
/*
 *---------------------------------------------------------------
 * App Config
 *---------------------------------------------------------------
 *
 * This Is The Main Configuration Of Project
 *
 * Do Not Edit Or Remove Any Key, You Can Edit Only The Values
 *
 */

# Site URL ex: domain.com or localhost/domain
$config['base_url'] = '';
# if you're using proxy http://192.168.137.1/domain
# port number is optional ex: 192.168.137.1:80
$config['http_proxy'] = '';

// if no route set, the router will match the uri with the controller/action
$config['enable_default_routing'] = true;

# Development Mode || Production Mode
$config['devmode'] = true; 

# Show database errors
$config['debug-db'] = false;

# Show exception in Json (only in dev mode)
$config['json_exception'] = false;

# Set Default Language (code)
# this used if we want to add multi-language support 
$config['lang_code'] = 'en';

# Show Errors Reporting
$config['errors'] = true;

# Time Zone
$config['timezone'] = 'Africa/Cairo';

# Page Not Found Controller
$config['not_found_controller'] = null;

/*
 *---------------------------------------------------------------
 * Parent Controller
 *---------------------------------------------------------------
 * - This act like a constructor, but in class context.
 * - Parent controller runs first before loading any controller.
 * - Parent Controller its not UI, which means id doesn't load views.
 * 
 * @example
 * - We can use it to register site analytics, like visitors
 */

 $config['parentController'] = '__ParentController';
 
/**
 * ----------------------------------------------------------------
 * Default Controller
 * ----------------------------------------------------------------
 * - Specify a default controller to load when no URI is present 
 *   and only baseurl is present, example: "site.com" (no controller)
 * - By-default, default controller set to "indexController"
 * which contains the "Hello world" when you first time installed the framework
 * - IF you changed the default controller to something else like: "homeController",
 *   Then make sure to create that controller
 * 
 * @see 
 * - You can skype this and set the default controller from `routes.php` 
 *   By using Route::map('/', exampleController::class)
 * - i.e  Route::map('/', indexController::class);
 */
$config['default_controller'] = "HomeController";

/**
 * ----------------------------------------------------------------
 * Default Action
 * ----------------------------------------------------------------
 * - Each controller should have a default action 
 *   when no action is present in the uri. 
 * - Assume the request url is "site.com/blog". here only blogController
 *   is present. when we load the blogController, we have to set a 
 *   default action to be invoked when no action specified in the uri
 * - The default action is set to "default", this means that we have
 *   to have an action named "default()" in our controllers.
 * @example 
 * - IF we have indexController and default, the expected structure should be:
 * class indexController extends Controller{
 *     function default(){
 *         echo "Hello world!";
 *     }
 * }
 * 
 * @see 
 * @see 
 * - You can skype this and set the default controller/action from `routes.php` 
 *   By using Route::map('/', exampleController::class, 'my_action)
 * - i.e  Route::map('/', indexController::class, 'default');
 */
$config['default_action'] = "default"; // function default(){..}



/*
 *---------------------------------------------------------------
 * Security
 *---------------------------------------------------------------
 *
 * Warning: Do Not Edit This Configs If You Are Not Sure
 * What Are You Doing
 * 
 */

# Start Session By Default 
$config['sess-start'] = false;

# Prevent Session Hijacking !!
$config['prev-sess-hi'] = true;

# Make Custom Session Name
$config['sess-name'] = 'cm-sess';

# Make Custom Session Dir-Name
$config['sess-dir'] = 'session';

# Turn on cookie_httponly - (recommended)
$config['http-only'] = true;

// decode url - set to true in case url has non-latin characters like arabic
$config['url_decode'] = true;




