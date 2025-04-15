<?php 
namespace Core\Classes;

use Core\Interfaces\IStrings;

class Strings implements IStrings{
    private static $INSTANCE;
    private static $current_language = self::DEFAULT_LANG_CODE;
    private static $lang_arrays = [];


    /**
     * =======================================================================
     * Private Constructor
     * =======================================================================
     * - Prevent create new instance of this class
     */
    private function __construct() {}

    /**
     * =======================================================================
     * Singleton Init
     * =======================================================================
     */
    public static function init($default_lang=null): Strings {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new self();
            if($default_lang!=null){
                self::$current_language = $default_lang;
            }
            self::load_languages();
        }
        return self::$INSTANCE;
    }


    
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
    public static function set_language(string $lang_code=Self::DEFAULT_LANG_CODE){
        self::$current_language = $lang_code;
    }

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
    public static function get(string $str_key, $lang_code=null){
        // Get the current language code from somewhere (e.g., session, cookie, etc.)
        $lang_code = ($lang_code!=null) ? $lang_code : self::$current_language;

        // Check if language exists
        if(!key_exists($lang_code, self::$lang_arrays)){
            if(is_devmode()){
                echo show_error("Undefined language code ".$lang_code);
            }
            return null;
        }

        // Check if string exists
        if(!key_exists($str_key, self::$lang_arrays[$lang_code])){
            if(is_devmode()){
                echo show_error("Undefined key ".$str_key);
            }
            return null;
        }

        // Return the translated string from the cached array
        return self::$lang_arrays[$lang_code][$str_key];
    }





    /*##############################[Private]##############################*/

    /**
     * -----------------------------------------------------------------------
     * Load Languages
     * -----------------------------------------------------------------------
     * - This should done once, when app started.
     * - User may store langs in json or xml, we'll try to load from both.
     */
    public static function load_languages(){

        // Load xml languages if exists
        @self::load_from_xml();
        @self::load_from_json();

    }



    /**
     * -----------------------------------------------------------------------
     * Load Langs From XML
     * -----------------------------------------------------------------------
     */
    private static function load_from_xml(){
        // get all files as array
        $langFiles = glob(LANGUAGES.DS.'*.xml');

        foreach ($langFiles as $file) {
            // language code, example: ar, en, es, etc
            $lang_code = basename($file, '.xml');
            // xml content
            $xml_content = simplexml_load_file($file);
            // get available languages code
            $lang_array = [];
            foreach ($xml_content->string as $string) {
                $lang_array[(string) $string['name']] = (string) $string;
            }
            self::$lang_arrays[$lang_code] = $lang_array;

        }
    }


    /**
     * -----------------------------------------------------------------------
     * Load Langs From JSON
     * -----------------------------------------------------------------------
     */
    private static function load_from_json(){
        // get all files as array
        $langFiles = glob(LANGUAGES.DS.'*.json');

        foreach ($langFiles as $file) {
            $langCode = basename($file, '.json');
            self::$lang_arrays[$langCode] = json_decode(file_get_contents($file), true);
        }
    }

}
