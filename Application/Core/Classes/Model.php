<?php
namespace Core\Classes;

use ApiStatus;
use Core\DatabaseHandler;
use Core\Interfaces\IApiMessage;
use Core\Interfaces\IApiStatus;
use Core\Interfaces\IDatabase;
use stdClass;

/*
 *---------------------------------------------------------------
 * CodeMediator - Model
 *---------------------------------------------------------------
 * 
 *
 */
class Model implements IApiMessage, IDatabase{
    private $statusCode= IApiStatus::_200_OK; // default is success
    private $error = null; // default is null
    private $ui_error=null;
    private $debugs = [];
    protected $db; // use this object to access database handler methods


    /**
     * =====================================================================
     * Constructor
     * =====================================================================
     */
    public function __construct()
    {
        $this->db =  new DatabaseHandler();
    }

    /**
     * =====================================================================
     * Access Db Methods
     * =====================================================================
     */
    public function db(){
        return $this->db;
    }


	/*=======================================================================*/ 
    /*                   Implement Interface Methods
    /*=======================================================================*/

    /**
     * ------------------------------------------------------------
     *  Set Status Code
     * -------------------------------------------------------------
     * - Set status code, like 200, 404, 500
     */
    public function setStatus(int $status_code){
        $this->statusCode = $status_code;
    }


    /**
     * ------------------------------------------------------------
     *  Set Error
     * -------------------------------------------------------------
	 * - Set status code & message
	 * - This will also update the status code
     * @example
	 * setError(404, "resource not found");
     */
    public function setError(int $error_code, $error_msg=null){
		$this->statusCode = $error_code;
        $this->error = new stdClass;
		$this->error->code = $error_code;
        $this->error->message = $error_msg;
    }

    /**
     * ------------------------------------------------------------
     *  Set UI Error
     * -------------------------------------------------------------
	 * - this error can be shown in ui (website pages)
	 * - This will also update the status code
     * @example
	 * setUiError("username is invalid");
     */
    public function setUiError(string $error_msg){
        $this->ui_error = $error_msg;
    }

    /**
     * ------------------------------------------------------------
     *  Add Debug
     * -------------------------------------------------------------
	 * - Provide additional info about the error
	 * - This should only used in dev mode
     */
    public function addDebug(string $key=null, $value){
        // first, lets escape any special characters
        $value = htmlspecialchars_any($value);
        $this->debugs[$key]=$value;
    }

    /**
     * ------------------------------------------------------------
     *  Get Status Code
     * -------------------------------------------------------------
     * - Get status code, like 200, 404, 500
     */
    public function getStatus():int {
        return $this->statusCode;
    }

    /**
     * ------------------------------------------------------------
     *  Get Ui Error
     * -------------------------------------------------------------
     * - this error can be shown in website pages
     */
    public function getUiError(){
        return $this->ui_error;
    }

    /**
     * ------------------------------------------------------------
     *  Get Error
     * -------------------------------------------------------------
     * - get errors if found
     */
    public function getError(){
        $errorObj = $this->error;
        // return error
        return $errorObj;
    }

    /**
     * ---------------------------------------------------------------
     * Get debugs
     * ---------------------------------------------------------------
     */
    public function getDebugs(): array{
        return $this->debugs;
    }

    /**
     * ------------------------------------------------------------
     *  Reset Status & Errors
     * -------------------------------------------------------------
     * - this can be called when the controller accept the final data from model
     * - So that any saved error will not be used in next call
     */
    public function resetStatus(){
        $this->statusCode=IApiStatus::_200_OK;
        $this->error = null;
    }
}