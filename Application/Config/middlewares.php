<?php 
/**
 * This class contains the middlewares that should be auto-loaded by the Router.
 * 
 */

/**
 * *******************************************************************************
 * Autoload Middlewares
 * *******************************************************************************
 * - Middlewares that should be auto-loaded are placed in this array.
 */
$autoload_middlewares = [
    // Place your own auto-loaded middlewares here
    MyAutoloadMiddleware::class

];

/**
 * *******************************************************************************
 * Middlewares Aliases
 * *******************************************************************************
 * - Aliases let you create a shortcut fo your full middleware class name.
 * @example 
 * - Original: IsLoggedInMiddleware::class
 * - Alias: "isLogin"
 */

$middlewares_aliases = [
    "isLogin" => IsLogInMiddleware::class
];