<?php

use Core\Interfaces\IMiddleware;

/**
 * *********************************************************************************
 * Autoload Middleware
 * *********************************************************************************
 * - This middleware can be autoloaded before any other regular middlewares.
 * - You have to register this middleware in config -> autoload_middlewares.
 */
class MyAutoloadMiddleware implements IMiddleware{

    /**
     * -------------------------------------------------------------------
     * handle Request
     * -------------------------------------------------------------------
     */
    public function handle(): ?bool{
        // Make your logic here and return boolean.
        return true;
    }
}