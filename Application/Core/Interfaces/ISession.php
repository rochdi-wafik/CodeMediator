<?php
namespace Core\Interfaces;

interface ISession{

    /**
     * Put Data
     */
    function put(string $key, $value);


    /**
     * - Store temp data for the next request
     * - This data will be unset when next request executed
     * @example
     * Session::flush("msg", "Hello user");
     */
    function flush(string $key, $value);

    /**
     * Pull: retrieve then delete the item from the session
     */
    function pull(string $key);

    /**
     * Delete data from session
     */
    function delete(string $key);

    /**
     * Clear session
     * - This will clear any saved data
     */
    function clear();

    /**
     * Check if key exists in session
     * This will return true if exists and null if not exists
     */
    function has(string $key);

    /**
     * Check if key exists in session, even with null value
     */
    function exists(string $key);

    /**
     * Regenerating the Session ID
     * This usually used to prevent users malicious from 
     * exploiting `session fixation attack`
     * - Saved data will not lost, only session id will be regenerated
     */
    function regenerate();

    /**
     * Regenerate Session ID & remove all old data
     */
    function invalidate();
}