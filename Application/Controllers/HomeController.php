<?php 
use Core\Classes\Controller;
use Core\Classes\View;

class HomeController extends Controller{
	private $customizeModel;
    private $settingModel;
	private $productModel;
	private $postModel;
	private $authService;



    /**
     * ==============================================================================
     *   Constructor
     * ==============================================================================
     */
    function __construct(){
		// init codes here..
    }

	/**
	 * ==============================================================================
	 *   Default
	 * ==============================================================================
	 */
	function default(){	

		// Set data that will be passed to Views
		View::setData('welcome', 'Welome To CodeMediator');

		// Load views
		View::load("layouts/header");
		View::load('home');
		View::load("layouts/footer");
	}


}