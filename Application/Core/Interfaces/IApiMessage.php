<?php
namespace Core\Interfaces;
interface IApiMessage{

    /**
     * ------------------------------------------------------------
     *  Set Status Code
     * -------------------------------------------------------------
     * - Set status code, like 200, 404, 500
     */
    public function setStatus(int $status_code);

    /**
     * ------------------------------------------------------------
     *  Set UI Error
     * -------------------------------------------------------------
	 * - this error can be shown in ui (website pages)
	 * - This will also update the status code
     * @example
	 * setUiError("username is invalid");
     */
    public function setUiError(string $error_msg);

    /**
     * ------------------------------------------------------------
     *  Set Error
     * -------------------------------------------------------------
	 * - Set status code & message
	 * - This will also update the status code
     * @example
	 * setError(404, "resource not found");
     */
    public function setError(int $error_code, string $error_msg=null);

    /**
     * ------------------------------------------------------------
     *  Add Debug
     * -------------------------------------------------------------
	 * - Provide additional info about the error
	 * - This should only used in dev mode
     */
    public function addDebug(string $key=null, $value);
    
    /**
     * ------------------------------------------------------------
     *  Get Status Code
     * -------------------------------------------------------------
     * - Get status code, like 200, 404, 500
     */
    public function getStatus(): int;

    /**
     * ------------------------------------------------------------
     *  Get Ui Error
     * -------------------------------------------------------------
     * - this error can be shown in website pages
     */
    public function getUiError();

    /**
     * ------------------------------------------------------------
     *  Get Error
     * -------------------------------------------------------------
     * - get errors if found
     */
    public function getError();

    /**
     * ------------------------------------------------------------
     *  Get Debug
     * -------------------------------------------------------------
     * - get errors if found
     */
    public function getDebugs() : array;

}