<?php 
namespace Core\Middlewares;

use Core\Interfaces\IMiddleware;

/**
 * *********************************************************************************
 * !DO NOT REMOVE THIS Class 
 * *********************************************************************************
 */
class __CsrfMiddleware implements IMiddleware{

    /**
     * -------------------------------------------------------------------
     * handle Request
     * -------------------------------------------------------------------
     */
    function handle(): ?bool{
        return true;
    }
}