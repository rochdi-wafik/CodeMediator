<?php 
namespace Core\Middlewares;

use Core\Interfaces\IMiddleware;

/**
 * *********************************************************************************
 * !DO NOT REMOVE THIS Class 
 * *********************************************************************************
 * - Limits the number of requests from a single IP address within a given time period 
 *  to prevent denial-of-service (DoS) attacks
 */
class __DosMiddleware implements IMiddleware{

    /**
     * -------------------------------------------------------------------
     * handle Request
     * -------------------------------------------------------------------
     */
    function handle(): ?bool{
        return true;
    }
}