<?php
namespace Core\Classes;

/**
 * ------------------------------------------------------------------------
 *               SharedData
 * ------------------------------------------------------------------------
 * - This class can be used to share data between multiple classes.
 * - The data is stored and retrieved within the context of app execution, 
 *   Which means the data not stored forever, 
 *   but it's destroyed after app executed (http response) like a variable.
 */
class SharedData {
    private static $instance;
    private $data = [];

    /**
     * -------------------------------------------------------------------
     * Private Constructor
     * -------------------------------------------------------------------
     * - Prevent create new instance, use Singleton instance instead.
     */
    private function __construct() {}

    /**
     * -------------------------------------------------------------------
     * Get Instance
     * -------------------------------------------------------------------
     * - Singleton pattern create instance once and re-use it.
     * - In this way, we can share data between child classes
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * ---------------------------------------------------------------------
     * Set Data
     * ---------------------------------------------------------------------
     * - Pass data from controller to view
     * - This data can be used inside views
     */
    function setData(string $key, $value): void{
        $this->data[$key] = $value;
    }

    /**
     * ---------------------------------------------------------------------
     * Get Data
     * ---------------------------------------------------------------------
     * - Get data that is set by setData()
     * - This function usually used by views to get data passed from controller
     * - But it still can be used between the functions inside the same controller
     * @param key the same key used in setData()
     * @param default_value to be used if key not found
     * @return mixed
     */
    function getData(string $key, $default_value=null){
        if(array_key_exists($key, $this->data)){
            return $this->data[$key];
        }
        return $default_value;
    }

    /**
     * ---------------------------------------------------------------------
     * Remove Specific Data
     * ---------------------------------------------------------------------
     * Remove specific data by its key
     * @param key the same key used in setData()
     * @return bool true if removed, else false
     */
    function remove(string $key): bool{
        if(array_key_exists($key, $this->data)){
            unset($this->data[$key]);
            return true;
        }
        // key not found
        return false;
    }

    /**
     * ---------------------------------------------------------------------
     * Get All Saved Data
     * ---------------------------------------------------------------------
     * - Get all stored data
     * @return array
     */
    function getAll(): array{
        return $this->data;
    }

    /**
     * ---------------------------------------------------------------------
     * Clear
     * ---------------------------------------------------------------------
     * - Clear all saved data
     */
    function clear(): void{
        $this->data = [];
    }
}