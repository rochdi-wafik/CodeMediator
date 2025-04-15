<?php
class DataStore{
    private static $data = [];

    /**
     * -------------------------------------------------------------------
     * Private Constructor
     * -------------------------------------------------------------------
     * - Prevent create new instance, use Singleton instance instead.
     */
    private function __construct() {}



    /**
     * ---------------------------------------------------------------------
     * Set Data
     * ---------------------------------------------------------------------
     * - Pass data from controller to view
     * - This data can be used inside views
     */
    public static function setData(string $key, $value): void{
        self::$data[$key] = $value;
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
    public static function getData($key, $default_value=null){
        if(array_key_exists($key, self::$data)){
            return self::$data[$key];
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
    public static function remove(string $key): bool{
        if(array_key_exists($key, self::$data)){
            unset(self::$data[$key]);
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
    public static function getAll(): array{
        return self::$data;
    }

    /**
     * ---------------------------------------------------------------------
     * Clear
     * ---------------------------------------------------------------------
     * - Clear all saved data
     */
    public static function clear(): void{
        self::$data = [];
    }
}