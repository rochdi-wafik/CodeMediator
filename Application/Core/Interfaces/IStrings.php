<?php
namespace Core\Interfaces;

interface IStrings{
    public const DEFAULT_LANG_CODE = 'en';

    /**
     * =======================================================================
     *   Set Language
     * =======================================================================
     * - Set language name where this class should get strings from.
     * - Use should already have lang files stored in 'languages/' 
     *   before using this class.
     * 
     * 
     * @example Strings::set_language('ar'); Set language to arabic
     * 
     * @param key the string key
     * @param lang_code  example: 'ar', 'en'
     * 
     */ 
    public static function set_language(string $lang_code=Self::DEFAULT_LANG_CODE);

    /**
     * =======================================================================
     *   Get Value
     * =======================================================================
     * - Get string value by key
     * - Use should already have lang files stored in 'languages/' 
     *   before using this class
     * - User should already set the lang name where this class should 
     *   load strings from.
     * 
     * 
     * @example get hello world
     *    Strings::get('hello_world'); output => 'Hello World'
     * 
     * @param key the string key
     * @param lang get from specific lang
     * 
     */ 
    public static function get(string $key, $lang=null);

}
