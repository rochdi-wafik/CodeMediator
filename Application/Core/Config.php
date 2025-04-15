<?php
namespace Core;
/*
 *-------------------------------------------------------------------
 * CodeMediator - Config
 *-------------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 * This File Has To Be Included In Index Of MVC Before Anything
 * 
 *
 */

defined('DS') OR define('DS', DIRECTORY_SEPARATOR); 

# push errors
$system['error'] = null;



/*
 *-------------------------------------------------------------------
 * Import Constants
 *-------------------------------------------------------------------
 *
 */

require_once dirname(dirname( __FILE__ )).DS.'Core'.DS.'Constants.php';

/*
 *-------------------------------------------------------------------
 * Import Functions
 *-------------------------------------------------------------------
 *
 */

require_once CORE.DS.'Functions.php';



/*
 *-------------------------------------------------------------------
 * Import User Config Files
 *-------------------------------------------------------------------
 *
 */

// Include Config/app 
if(file_exists(CONFIG.DS.'app.php'))
{
	include_once CONFIG.DS.'app.php';
}else{
	$system['error'] .= show_msg('<li>File config/app.php is not exists !</li>', DANGER);
}

// Include Config/database 
if(file_exists(CONFIG.DS.'database.php'))
{
	include_once CONFIG.DS.'database.php';
}else{
	$system['error'] .= show_msg('<li>File config/database.php is not exists !</li>',DANGER);
}

// Include Config/middlewares 
if(file_exists(CONFIG.DS.'middlewares.php'))
{
	include_once CONFIG.DS.'middlewares.php';
}else{
	$system['error'] .= show_msg('<li>File config/middlewares.php is not exists !</li>', DANGER);
}

//Include Config/routes 
// if(file_exists(CONFIG.DS.'routes.php'))
// {
// 	include_once CONFIG.DS.'routes.php';
// }else{
// 	$system['error'] .= show_msg('<li>File config/routes.php is not exists !</li>',DANGER);
// }

// Include Config/constants 
if(file_exists(CONFIG.DS.'constants.php'))
{
	include_once CONFIG.DS.'constants.php';
}else{
	$system['error'] .= show_msg('<li>File config/constants.php is not exists !</li>',DANGER);
}

/*
 *-------------------------------------------------------------------
 * Global Configuration
 *-------------------------------------------------------------------
 *
 */

if(!is_devmode())
{
	error_reporting(0);
}

/*
 *-------------------------------------------------------------------
 * Sessions Configuration
 *-------------------------------------------------------------------
 *
 */

// Custom Session Directory 
if(is_config('sess-dir'))
{
	$sess_path = APP.DS.get_config('sess-dir');
	if(!is_dir($sess_path)){
		@mkdir($sess_path);
	}
	if(is_dir($sess_path)){
		if(session_save_path() != $sess_path){
		    @session_save_path($sess_path);
		}
	}
}

// Rename Session Name 
if(is_config('sess-name'))
{
    @session_name(get_config('sess-name'));
}else{
	@session_name('cm-sess');
}

// Prevent js to handle sess-cookie 
if(verify_config('http-only', true))
{
  @ini_set('session.cookie_httponly', true);
}

// Prevent Session Hijacking 
if(verify_config('prev-sess-hi', true))
{
    prevent_sess_hijacking();
}

// Clear Empty Session Files 
if(is_config('sess-dir'))
{
	$path = APP.DS.get_config('sess-dir');
	
	foreach (scandir($path) as $file)
	{
		if(is_file($path.DS.$file))
		{
			if(filesize($path.DS.$file) == 0)
			{
		        @unlink($path.DS.$file);
		    }
		}
	}
}

// Start session in all pages
if(verify_config('sess-start', true))
{
	@session_start(); 
}



// Set Time Zone 
if(is_config('timezone'))
{
	date_default_timezone_set(get_config('timezone'));
}

// Format Exceptions To Json (DevMode)
if(is_config('json_exception')){
	if(get_config('json_exception') === true && is_devmode()){
		set_exception_handler('custom_exception_handler');
		set_error_handler('custom_error_handler',-1);
	}
} 
// (DevMode)
function show_json_exception(){
	if(is_devmode()){
		set_exception_handler('custom_exception_handler');
	    set_error_handler('custom_error_handler',-1);
	}
}

// disable cache
if(is_devmode()){
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');
	header('Expires: 0');
}
