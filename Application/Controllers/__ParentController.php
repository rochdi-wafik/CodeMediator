<?php

/**
 * *********************************************************************
 *   @ParentController
 * *********************************************************************
 * - This Controller is always executed before any other controllers.
 * - It can be used for init purposes, like register new visit action.
 * - This controller is View-less. it should not be used to load views.
 */
use Core\Classes\Controller;

class __ParentController extends Controller{

    /**
     * *********************************************************************
     *   Constructor
     * *********************************************************************
     */
    public function __construct(){
        // Init logic here
        // Example: Register user visit, analyze traffic, etc.
    }

}
