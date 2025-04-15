<?php 
namespace Core;

use PDO;
use PDOException;

/*
 *---------------------------------------------------------------
 * CodeMediator - Database
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 * 
 * This Class Connect To The Database And Return The Connection
 * Used By Model To Run The DB Methods Like (insert-update-etc)
 *
 */
class Database
{
	private $hostname;
	private $database;
	private $username;
	private $password;
	private $charset;
	private $system; // @todo change to $db_type

	// Singleton
    private static $INSTANCE = null;
	private $connection=null;


    /**
     * ==============================================================================
     *  Get Instance 
     * ==============================================================================
     * - Singleton help reduce database connections.
	 * - Instead of creating new connection every time we need to apply query,
	 *   we create connection one time and used to apply all queries.
	 * - This will avoid "too many MySQL connections" error, and improve reply speed 
	 *   especially under limited resource web hosting.
     */
    public static function getInstance(): Database{

        if(self::$INSTANCE===null){

            self::$INSTANCE = new self();
        }

        return self::$INSTANCE;

    }

	/**
	 * ------------------------------------------------------------------------
	 * Private Constructor
	 * ------------------------------------------------------------------------
	 * - This class cannot be instantiated, use getInstance() instead
	 * - Using singleton, connection created one time and used everywhere
	 */
	private function __construct()
	{
		global $database; // get $database variable from Config/database.php
		$this->hostname = $database['hostname'];
		$this->database = $database['database'];
		$this->username = $database['username'];
		$this->password = $database['password'];
		$this->charset  = $database['charset'];
		$this->system   = $database['system']; // mysql

		// Establish db connection
		$this->connection = $this->connect();
	}
	
	public function connect()
	{
		$pdo = null;
		try
		{
			$pdo = new PDO($this->system.":host=".$this->hostname.";dbname=".$this->database.";charset=".$this->charset,$this->username,$this->password,array(
				PDO::ATTR_PERSISTENT => true //cache connection to re-used
			));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			/**
			 * IF Connection failed, it could be no db created yet,
			 * The user my need to install the script from installation page
			 */
			if(is_devmode())
			{
				$msg  = show_msg("<b>Faild Connecting To Database:</b>&nbsp;{$e->getMessage()}", DANGER);
				$msg .= show_msg("<b>Check your database configuration in <a>config/database.php</a> ", WARNING);
				$msg .= show_msg("<b>Try to install database correctly from </b> <a href='".base_url('install')."'>here</a> ", WARNING);
				die(show_error($msg, "Database Connection Failed",tracerout(false)));
			}
			die(page_404());
		}
		return $pdo;
	}

	/**
	 * --------------------------------------------------------------------
	 * Get Connection
	 * --------------------------------------------------------------------
	 */
	public function getConnection(){
		return $this->connection;
	}

	/**
	 * --------------------------------------------------------------------
	 * Close Connection
	 * --------------------------------------------------------------------
	 */
	public function closeConnection(){
		$this->connection = null; // Null the PDO object to allow garbage collection
        self::$INSTANCE = null; // Reset the singleton instance
	}

}