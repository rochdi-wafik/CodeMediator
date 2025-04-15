<?php 
namespace Core\Middlewares;

use Core\Interfaces\IMiddleware;

/**
 * *********************************************************************************
 * !DO NOT REMOVE THIS Class 
 * *********************************************************************************
 * - Sanitize user input to prevent such attacks like XSS or SQL Injection.
 */
class __UserInputMiddleware implements IMiddleware{

    /**
     * -------------------------------------------------------------------
     * handle Request
     * -------------------------------------------------------------------
     */
    function handle(): ?bool{
        return true;
    }
}