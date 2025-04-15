<?php
namespace Core\Interfaces;

use Core\Classes\Cache;

interface ICache{
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
    static function config(array $conf): void;

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
    static function table(string $name, int $ttl = 3600*24): Cache;

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
    function set(string $key, $value, $ttl=3600*24): Cache;

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
     * ===========================================================================
     * @todo Add Where Condition:
     * - IF we have a big List of items saved, user may only need to fetch specific
     *   item by a condition, like by id or name.
     * - So we don't have to fetch all items to get one item.
     */
    function get(string $key, $default=null);

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
     *   users then we loop to get the right element.
     * - We can use this method to get the specified user directly.
     * @return mixed
     * ===========================================================================
     * @example
     * [-] Save simple data:
     * $    table('tbl')->getWhere('users', ['id' => 2]);
     */
    function getWhere(string $data_key,  array $where, bool $fetch_single=true, $default=null);

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
    function pull(string $key, $default=null);

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
    function has(string $key): bool;


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
    function remove(string $key): bool;


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
    function clean(): bool; 

    /**
     * ***************************************************************************
     * Flush Cache
     * ***************************************************************************
     * - clean the entire cache with all tables.
     * ===========================================================================
     * @example
     * $    Cache::flush();
     */
    static function flush(): bool;

}