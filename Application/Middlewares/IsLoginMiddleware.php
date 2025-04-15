<?php

use Core\Interfaces\IMiddleware;

/**
 * *********************************************************************************
 * IS Logged In Middleware
 * *********************************************************************************
 * - Check if client is logged in 
 */
class IsLoginMiddleware implements IMiddleware{

    /**
     * -------------------------------------------------------------------
     * handle Request
     * -------------------------------------------------------------------
     */
    public function handle(): ?bool{
        //  Make your logic here and return boolean.
        return false;
    }
}