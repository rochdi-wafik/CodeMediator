<?php 
namespace Core\Middlewares;

use Core\Interfaces\IMiddleware;

/**
 * *********************************************************************************
 * !DO NOT REMOVE THIS Class 
 * *********************************************************************************
 * - Log incoming requests for debugging purposes
 */
class __LogMiddleware implements IMiddleware{

    /**
     * -------------------------------------------------------------------
     * handle Request
     * -------------------------------------------------------------------
     */
    function handle(): ?bool{
        return true;
    }
}