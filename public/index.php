<?php
/* 
 *---------------------------------------------------------------
 * CodeMediator - A PHP Web Framework Based On MVC
 *---------------------------------------------------------------
 *
 * This Is The Index Of The Project
 * Do Not Edit Or Remove Any Code In This page
 *
 * Make Sure To Setup The Server Connection First
 * The Setup File In: application/config/database.php
 *
 * Version 1.0
 *
 */

use Core\App;

defined('DS') OR define('DS', DIRECTORY_SEPARATOR);

error_reporting(E_ALL);
ini_set('display_errors', 1);
/* 
 *---------------------------------------------------------------
 * Check .htaccess
 *---------------------------------------------------------------
 *
 */

if(!file_exists(dirname(__DIR__).DS.".htaccess")):
	$msg  = "The [.htaccess] file does not exists on root directory, you have to upload this file to run your application <br>";
	die($msg);
endif;

/*
 * ---------------------------------------------------------------
 * Include Config & Autoloader
 * ---------------------------------------------------------------
 * 
 */

require_once dirname(__DIR__).DS.'Application'.DS.'Core'.DS.'Config.php';
// Framework Autoloader
require_once CORE.DS.'Autoload.php';
// Composer Autoloader
require_once LIBS.DS.'vendor'.DS.'autoload.php';
// User routes
require_once CONFIG.DS.'routes.php';

// Run App
new App();
