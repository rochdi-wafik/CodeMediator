<?php  
namespace Core;
/*
 *---------------------------------------------------------------
 * CodeMediator - App
 *---------------------------------------------------------------
 * 
 * Do Not Edit Or Remove Anything From This File 
 * This Is The Class Which Will Run The Whole Project
 *
 */

class App
{
	function __construct()
	{
		global $system;

		// [-] Check if there is any errors before start
		if(!empty($system['error']))
		{
			die(show_error($system['error']));	
		}

		// [-] Initialize Multi-Lang Support
		// Strings::init('en');

		// [-] Initialize Router;
		new Router();
	
	}

}
