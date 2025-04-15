<?php 

/*
 *-------------------------------------------------------------------
 * CodeMediator - Autoload
 *-------------------------------------------------------------------
 *
 * Do Not Remove Anything From This File 
 *
 * This File Will Load The Required Classes Automatically 
 * This File Has To Be Included In Index Of CodeMediator  
 * 
 *
 */

spl_autoload_register(function($class)
{ 

	/**
	 * (1) IF Namespace
	 */
	// IF Namespace: Replace backslashes (\\) with directory separators (/)
    $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
	if (file_exists(APP.DS .$classFile)) {
        require_once APP.DS .$classFile;
        return;
    }

	/**
	 * (2) IF No Namespace
	 */

	// Folder : Config 
	if(file_exists(CONFIG.DS.$class.'.php'))
	{
		require_once CONFIG.DS.$class.'.php';

	}
	// Folder : Core 
	if(file_exists(CORE.DS.$class.'.php'))
	{
		require_once CORE.DS.$class.'.php';

	}

	// Folder : Core/Classes
	if(file_exists(CORE.DS.'classes'.DS.$class.'.php'))
	{
		require_once CORE.DS.'classes'.DS.$class.'.php';
	}

	// Folder : Core/interfaces
	if(file_exists(CORE.DS.'interfaces'.DS.$class.'.php'))
	{
		require_once CORE.DS.'interfaces'.DS.$class.'.php';
	}

	// Folder : Helper
	if(file_exists(HELPER.DS.$class.'.php'))
	{
		require_once HELPER.DS.$class.'.php';

	}

    // Folder : Controllers
	if(file_exists(CONT.DS.$class.'.php'))
	{
		require_once CONT.DS.$class.'.php';
	}

	// Folder : Models
	if(file_exists(MODELS.DS.$class.'.php'))
	{
		require_once MODELS.DS.$class.'.php';
	}

	// Folder : Classes 
	if(file_exists(CLASSES.DS.$class.'.php'))
	{
		require_once CLASSES.DS.$class.'.php';
	}

	// Folder : Services 
	if(file_exists(SERVICES.DS.$class.'.php'))
	{
		require_once SERVICES.DS.$class.'.php';
	}

	// Folder : User Middlewares 
	if(file_exists(MIDDLEWARES.DS.$class.'.php'))
	{
		require_once MIDDLEWARES.DS.$class.'.php';
	}

	// Folder : Core Middlewares 
	if(file_exists(CORE_MIDDLEWARES.DS.$class.'.php'))
	{
		require_once CORE_MIDDLEWARES.DS.$class.'.php';
	}
	
});