<?php
namespace Core\Classes;

use Core\Interfaces\ICache;
use Exception;
use stdClass;


class Cache implements ICache{
    private static $INSTANCE;
    public const DATA_COLUMN = 'table_data';
    public static $cacheDir = CACHE;
    public static $config = CACHE.DS.'.CONF';
    public static $cache_ttl =  (3600*24*6); // 7 days
    public static $table_ttl = (3600*24); // 1 day
    public static $last_error=null;

    // current opened table
    private static $current_table;

    /**
     * *************************************************************************
     * Get Instance Context
     * *************************************************************************
     */
    private static function getContext() : Cache{
        if (self::$INSTANCE === null) {
            self::$INSTANCE = new self();
        }
        return self::$INSTANCE;
    }


    /**
     * ***************************************************************************
     * Config
     * ***************************************************************************
     * - Set cache configuration like path, expiration time, etc.
     * @param path default is __BASE__\Application\Cache
     * @param ttl Time-To-Live for all cached tables, default is 7 Days.
     * ===========================================================================
     * @example
     * $    Cache::config([
     * $        "path" => CACHE, // (Optional) cache directory path.
     * $        "ttl" => 3600*60*24, // (Optional) time to live for all tables.
     * $    ]);
     */
    static function config(array $conf): void{
        if(key_exists('path', $conf)) self::$cacheDir = $conf['path'];
        if(key_exists('ttl', $conf)) self::$cache_ttl = $conf['ttl'];

        // Check cache dir
        if(!is_dir(self::$cacheDir)){
            if (!mkdir(self::$cacheDir, 0777, true)) {
                self::$last_error = "Unable to create cache directory";
                return;
            } 
            // Update config path
            self::$config = self::$cacheDir.DS.'.CONFIG';
        }
        // Check config file
        if(!is_file(self::$config)){
            file_put_contents(self::$config, json_encode(['ttl' => time()+self::$cache_ttl]));
            if(!is_file(self::$config)){
                self::$last_error = "Unable to create cache config file";
                return;
            }
        }

        // Check if cache should be clean (is expired)

    }


    /**
     * ***************************************************************************
     * Table
     * ***************************************************************************
     * - Specify or Create in cache directory.
     * - IF table is not found, it will be created in the path set by config([..]).
     * @param ttl Time-To-Live in seconds, default is 7 Days.
     * @return ICache self.
     * ===========================================================================
     * @example
     * [-] Create table in cache dir:
     * $    Cache::table('settings');
     * 
     * [-] Create table if not created, and set/get data
     * $    Cache::table('settings')->set('app_id', 'foo');
     * $    Cache::table('settings')->get('app_id');
     */
    static function table(string $name, int $ttl = 3600*24): \Core\Classes\Cache{
        // (1) Specify current table
        self::$current_table = self::$cacheDir.DS.'table_'.$name.'.json';
        self::$table_ttl = $ttl;
        // (2) IF table exists, check if his cache expired
        if(is_file(self::$current_table)){
            $tbl = json_decode(file_get_contents(self::$current_table), false);
            // if current time become greater that expiration time
            if($tbl->ttl < time()){
                // cache expired, clean table
                unlink(self::$current_table);
            }
        }
        // (3) IF table not exists (because clean or because first time)
        if(!is_file(self::$current_table)){
            // Create table and put ttl
            file_put_contents(self::$current_table, json_encode(['ttl' => time()+self::$table_ttl]));
            // Is table created
            if(!is_file(self::$config)){
                self::$last_error = "Unable to create table {$name}";
            }
        }
        return self::getContext();

    }

    /**
     * ***************************************************************************
     * Set Entry
     * ***************************************************************************
     * - Set data in cached table using key-value pair.
     * - If key already exists, the value will be override.
     * @param ttl time to live in seconds, default is 1 hour.
     * @return ICache self.
     * ===========================================================================
     * @example
     * [-] Save simple data:
     * $    table('credentials')->set('api_key', 'foo');
     * 
     * [-] With Chain
     * $    table('settings')->set('foo', 'bar')->set('x', 'y');
     * 
     * [-] Save complex data:
     * $    table('users')->set([
     * $       'name' => 'keven',
     * $       'age' => 23,
     * $       'is_admin' => false
     * $    ]);
     */
    function set(string $key, $value, $ttl=3600*24): \Core\Classes\Cache{
        $record = array(
            'id' => md5($key),
            'data_type' => gettype($value),
            'key' => $key,
            'value' => $value
        );

        // (1) Get current table data
        if(!is_file(self::$current_table)) return self::getContext();
        $table_data = json_decode(file_get_contents(self::$current_table));

        // (2) Create field table_data if not created
        if(!property_exists($table_data, self::DATA_COLUMN)){
            $table_data->{self::DATA_COLUMN} = [];
        }

        // (3) remove any old record
        $saved_data = $table_data->{self::DATA_COLUMN};
        $saved_data = $this->unset_item_by_key($saved_data, $key);
        
        // (4) Save the record
        $saved_data = array_merge($saved_data, [$record]);
         
        // (5) Save updated data
        $table_data->{self::DATA_COLUMN} = $saved_data;
        file_put_contents(self::$current_table, json_encode($table_data));
        return self::getContext();
    }

    /**
     * ***************************************************************************
     * Get Entry
     * ***************************************************************************
     * - get data from cached table using key.
     * - A default value can be returned if entry not found.
     * @return mixed
     * ===========================================================================
     * @example
     * [-] Save simple data:
     * $    table('tbl')->get('key');
     */
    function get(string $key, $default=null){
        // (1) Get current table data
        if(!is_file(self::$current_table)) return $default;
        $table_data = json_decode(file_get_contents(self::$current_table));
        // (2) Check if table main column exists
        if(!property_exists($table_data, self::DATA_COLUMN)) return $default;

        // (3) Check if specified key not exists
        if(!($item = self::get_item_by_key($table_data->{self::DATA_COLUMN}, $key))) return $default;

        // (4) Return found result
        return  $item->value;
    }

    /**
     * ***************************************************************************
     * Get Entry By Condition
     * ***************************************************************************
     * - get specific item in a list by key.
     * - We may have a large list stored by key, like:
     *    {
     *       "users" => [{id:1, ..}, {id:2, ..}, {id:3, ..}, {id:9999, ..}]
     *    }
     * - IF we want to get a specific user by its id, we don't have to fetch all
     *   users manually and then we loop to get the right element.
     * - We can use this method to get the specified user by condition (i.e id).
     * @return mixed
     * ===========================================================================
     * @example
     * [-] Save simple data:
     * $    table('tbl')->getWhere('users', ['id' => 2]);
     */

    function getWhere(string $data_key,  array $where, bool $fetch_single=false, $default=null){
        // Validate params
        if(!is_assoc_array($where)) return $default;
        // (1) Get current table data
        if(!is_file(self::$current_table)) return $default;
        $table_data = json_decode(file_get_contents(self::$current_table));
        // (2) Check if table main column exists
        if(!property_exists($table_data, self::DATA_COLUMN)) return $default;

        // (3) Check if specified key not exists
        if(!($item = self::get_item_by_key($table_data->{self::DATA_COLUMN}, $data_key))) return $default;

        // (4) Check if result is an normal array (List), not assoc array.
        $result_list = $item->value;
        if(!is_list($result_list)){
            // This is not List array (maybe assoc array)
            return $default;
        }

        // Filter the result
        $filtered_result = array_filter($result_list, function($item) use ($where) {
            foreach ($where as $key => $value) {
                // element must be ether array or object
                if(!is_array($item) && !is_object($item)) return false;
                // case array
                if(is_array($item)){
                    // key!=value: not match
                    if (!isset($item[$key]) || $item[$key] != $value) return false; 
                }
                // case object
                if(is_object($item)){
                    // key!=value: not match
                    if (!isset($item->$key) || $item->$key != $value) return false; 
                }
                
            }
            return true; 
        });
        // Get One Result
        if($fetch_single){
            return ($filtered_result==null || empty($filtered_result)) ? null : reset($filtered_result); // reset return first element
        }
        // Get all matched result (array)
        return $filtered_result;

    }

    /**
     * ***************************************************************************
     * Get & Delete
     * ***************************************************************************
     * - get data from cached table and remove it.
     * - A default value can be returned if entry not found.
     * @return mixed
     * ===========================================================================
     * @example
     * $    table('tbl')->pull('key');
     */
    function pull(string $key, $default=null){
        // (1) Check IF Not found
        $record = $this->get($key, null);
        if($record==null) return $default;

        // (2) Unset the item
        $table_data = json_decode(file_get_contents(self::$current_table));
        $table_data->{self::DATA_COLUMN} = $this->unset_item_by_key($table_data->{self::DATA_COLUMN}, $key);
             
        // (3) Save updated data
        file_put_contents(self::$current_table, json_encode($table_data));

        // (4) Return the item
        return $record;

    }

    /**
     * ***************************************************************************
     * Has Entry
     * ***************************************************************************
     * - check if cached table has an entry by key.
     * @return bool TRUE if success or FALSE.
     * ===========================================================================
     * @example
     * $    if(table('credentials')->has('secret_key')){
     * $        echo 'Found!'.
     * $    }
     */
    function has(string $key): bool{
        return !is_null($this->get($key, null));
    }


    /**
     * ***************************************************************************
     * Remove Entry
     * ***************************************************************************
     * - remove data from cached table by key.
     * @return bool TRUE if success or FALSE.
     * ===========================================================================
     * @example
     * $    table('foo')->remove('bar');
     */
    function remove(string $key): bool{
        // if key found, pull will return it and remove it.
        // if key not found, pull() will return null.
        return !is_null(self::pull($key));
    }


    /**
     * ***************************************************************************
     * Clean Table
     * ***************************************************************************
     * - clean specific cache table.
     * @return bool TRUE if success or FALSE.
     * ===========================================================================
     * @example
     * [-] clean specific table.
     * $    table('foo')->clean();
     */
    function clean(): bool{
        // check dir
        if (!is_dir(self::$cacheDir)) return false;
        // is exists
        if(is_file(self::$current_table)){
            try{
                unlink(self::$current_table);
                return true;
            }catch(Exception $e){
                self::$last_error = $e->getMessage();
            }
        }
        return false;
    }

    /**
     * ***************************************************************************
     * Flush Cache
     * ***************************************************************************
     * - clean the entire cache with all tables.
     * ===========================================================================
     * @example
     * $    Cache::flush();
     */
    static function flush(): bool{
        // check dir
        if (!is_dir(self::$cacheDir)) return false;
        // get files
        $files = glob(self::$cacheDir.DS.'*.json');  
        // iterate on files
        foreach($files as $file) { 
            // remove file
            if(is_file($file)){
                try{ unlink($file); }catch(Exception $e){}
            } 
        }
        return true;
    }


    #############################################################################
    ################################## Helpers ##################################
    #############################################################################

    /**
     * ==========================================================================
     * Get Entry By Key
     * ==========================================================================
     */
    private static function get_item_by_key($data, string $key): ?stdClass{
        if(!is_array($data)) return null;

        $items = array_filter($data, function($item) use ($key) {
            return $item->key == $key || $item->id == md5($key);
        });
        return ($items==null || empty($items)) ? null : reset($items); // reset return first element
    }

    /**
     * ==========================================================================
     * Unset Entry By Key
     * ==========================================================================
     */
    private static function unset_item_by_key($data, string $key): array{
        if(!is_array($data)) return [];
        $result = array_filter($data, function($item) use ($key) {
            return $item->key != $key;
        });
        return $result;
    }
}