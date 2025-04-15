<?php

use Core\Classes\Controller;
use Core\Classes\Route;

/*
 * ********************************************************************
 * Custom Routes
 * ********************************************************************
 *
 * This File Let You Make Your Own Routes
 * By Re-map Requests To A Specific Controllers Action And Params
 * - You must call Route::init() before start using Route class
 *     
 *     !Read the documentation to learn more about routing system   
 *  
 * 
 *
 */
// Init Route class

Route::init();

// Handle Pages
Route::get('about-us', function(){
    Controller::load(PageController::class, 'default', 'about-us');
});
Route::get('contact-us', function(){
    Controller::load(PageController::class, 'default', 'contact-us');
});
Route::get('privacy-policy', function(){
    Controller::load(PageController::class, 'default', 'privacy-policy');
});