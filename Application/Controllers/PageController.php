<?php 

/**
 * *********************************************************************
 *   @PageController
 * *********************************************************************
 * - This Controller will handle pages like about-us` and `contact-us`.
 * - We'll use Model and Service to get page content.
 * - Usually, data should be fetched from db, but since this is a demo, 
 *   We'll get data statically.
 * 
 */
use Core\Classes\Controller;
use Core\Classes\View;

class PageController extends Controller{
	private DatabaseService $databaseService;


    /**
     * *********************************************************************
     *   Constructor
     * *********************************************************************
     */
    public function __construct(){
		
		// init vars
		$this->databaseService = DatabaseService::getInstance();
    }

	/**
	 * *********************************************************************
	 *   Default
	 * *********************************************************************
	 */
	public function default(string $page_slug){	
		// IF no page slug is provided, show 404.
		if(empty($page_slug)){
			echo page_404();
			exit;
		}

		// Get page from Service
	    $page = $this->databaseService->getPage($page_slug);

		// Pass page data to views
		View::setData('page', $page);
		View::setData('page_title', $page->getTitle());


		// Load views
		View::load("layouts/header");
		View::load('page');
		View::load("layouts/footer");
	}


}