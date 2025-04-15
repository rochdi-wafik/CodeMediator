<?php
use Core\Classes\Service;
use Core\DatabaseHandler;

/**
 * ************************************************************************************
 *   @DatabaseService
 * ************************************************************************************
 * - This Service class should do all the logic related to database, like create, read, 
 *   update, delete.
 * - Since we don't have a database, we'll use static data for demo.
 * - DatabaseHandler is the class responsible to perform actions on tour database.
 * - We recommend to use singleton pattern when you need to get a Service instance.
 */
class DatabaseService extends Service{

    // Singleton instance
    private static $INSTANCE;


    /**
     * *********************************************************************
     *   Properties
     * *********************************************************************
     * @var db represent DatabaseHandler to perform actions on your database.
     *         Like Create, Read, Update, Delete.
     * @var context you may need it to pass context of this class to another
     *      classes.
     */
    private $db;
    private $context;

    /**
     * *********************************************************************
     *   Get Instance
     * *********************************************************************
     * - Follow Singleton pattern to get instances
     */
    public static function getInstance(): self{
        if(!self::$INSTANCE){
            self::$INSTANCE = new self();
        }
        return self::$INSTANCE;
    }

    /**
     * *********************************************************************
     *   Constructor
     * *********************************************************************
     * - Initialize necessary instances and vars, like db, context, etc.
     */
    public function __construct() {
        // Use @DatabaseHandler to get Database Utils, example:
        // $this->db = new DatabaseHandler(); 
        $this->context = $this;
    }


    /**
     * *********************************************************************
     *   Get Page
     * *********************************************************************
     * - Get page by slug name.
     * - Usually, pages should be fetched from db from table named `pages`. 
     *   And result will be presented to PageModel.
     *   But since this is a demo, we'll return pages statically.
     */
    public function getPage(?string $page_slug): ?PageModel{
        // IF null
        if($page_slug==null) return null;
        // Create title from slug
        $title = "Home";
        if($page_slug == 'about-us') $title = "About Us";
        if($page_slug == 'contact-us') $title = "Contact Us";
        if($page_slug == 'privacy-policy') $title = "Privacy Policy";

        // Create static page for testing
        $time = new DateTime();
        $page = new PageModel(
            rand(1, 100),
            $page_slug,
            $title, 
            $this->getDemoContent(),
            $time->getTimestamp()

        );

        // Return page
        return $page;
    }


    /**
     * *********************************************************************
     * Get Demo Content
     * *********************************************************************
     */
    private function getDemoContent(): string{
        return "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. \nExcepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    }


}