<?php 
namespace Core;

/**
 * -------------------------------------------------------------------------------
 * CORE URI
 * -------------------------------------------------------------------------------
 *  This class help us to get information about uri
 *  The situation in localhost is different than live server
 *  In live server: we may have url like this 
 *       http://sitename.com/controller/action/args
 *  In localhost: we may have url like this 
 *       http://localhost/projects/appname/controller/action/args
 *  In localhost: the project may exist in subfolder instead of root folder www
 * 
 * -------------------------------------------------------------------------------
 *  @method http()     : return http:// or https://
 *  @method baseUrl()  : return the base url of application
 *  @method hostname() : return the actual hostname
 *  @method requestUri : return the real REQUEST_URI @todo what means by real?
 *  @method isLocalhost: return true or false
 * 
 * -------------------------------------------------------------------------------
 *  URI ON Live Server |
 *  -------------------
 *  @link   http://sitename.com/controller/action/args
 *  @method http()       => http://
 *  @method hostname()   => sitename.com
 *  @method appPath()    => NULL
 *  @method baseUrl()    => http://sitename.com
 *  @method requiteUri() => /controller/action/args
 * 
 * -------------------------------------------------------------------------------
 *  URI ON Localhost  |
 *  -------------------
 *  @link   http://localhost/projects/appname/controller/action/args
 *  @method http()       => http://
 *  @method hostname()   => localhost
 *  @method appPath()    => /projects/appname
 *  @method baseUrl()    => http://localhost/projects/appname 
 *  @method requiteUri() => /controller/action/args
 * 
 *  $_SERVER['REQUEST_URI'] cause will return '/projects/appname/controller/action/args'
 *  So we use requestUri() instead, See DOC->URI to get details
 * -------------------------------------------------------------------------------
 * @method requestUri($case) 
 *         
 */

class URI
{

    /**
     * @return string http:// or https
     * 
     */
    static function getHttp()
    {
        return isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'].'://' : 'http://';
    }

    //--------------------------------------------------------------------------------

    /**
     * @return boolean true if localhost
     * 
     */
    static function isLocalhost()
    {
        if($_SERVER['HTTP_HOST'] == 'localhost' OR is_IP($_SERVER['HTTP_HOST']))
        {
            return true;
        }   
        return false;
    }

    //--------------------------------------------------------------------------------

    /**
     * This method used when the project folder is in sub folders 
     * @param const $sensitive : CASE_LOWER  CASE_UPPER
     * should we lowecase the uri before start loop or not
     * @return string hostname
     * 
     */
    static function getAppPath()
    {
        if(self::isLocalhost())
        {
            // get name of app folder using const 'base'
            $appname = strtolower(basename(BASE));
            $uri = strtolower(trim($_SERVER['REQUEST_URI'],'/'));
            $app_path = '';
            foreach (explode('/', $uri) as $name){
                if($name != $appname){
                    $app_path .= $name.'/';
                }else{
                    $app_path .= $appname;
                    break;
                }
            }
            return $app_path;
        }
        return;
    }

    //--------------------------------------------------------------------------------

    /**
     * @return string site url
     * 
     */
    static function getBaseUrl()
    {
        // IF base_url isset manually
        global $config;
        if(!empty($config['base_url']))
        {
            // remove any 'http://' or '/' 
            $base_url = str_replace('https://', '', $config['base_url']);
            $base_url = str_replace('http://', '', $base_url);
            $base_url = rtrim($base_url, '/');
            return self::getHttp().$base_url;
        }

        // ELSE : IF localhost
        if(self::isLocalhost())
        {
            // return http:// + localhost + / + app-path
            return self::getHttp().$_SERVER['HTTP_HOST'].'/'.self::getAppPath();
        }

        // ELSE : IF live server
        return self::getHttp().$_SERVER['HTTP_HOST'];
    }

   //--------------------------------------------------------------------------------

    /**
     * @param const $sensitive : CASE_INSENSITIVE | CASE_SENSITIVE
     * @return string real request uri
     * Request uri = (full_path - app_path)
     * @example 
     * @link full_path = /projects/myapp/controller/action/arg
     * @link app_path = /projects/myapp/
     * @link request  = controller/action/arg
     */
    static function getRequestUri($sensitive = null){
        $app_path = self::getAppPath() ?? ""; 
        $uri = $_SERVER['REQUEST_URI'] ?? "";

        if($sensitive == CASE_SENSITIVE)
        {
            $uri = str_replace($app_path,'',$uri);   
        }
        else{
            $uri = str_replace(strtolower($app_path),"",strtolower($uri)); 
            
        }
       
        // clean uri from any slashes
        $uri = trim($uri, '/'); 
        $uri = str_replace('///', '/', $uri);
        $uri = str_replace('//', '/', $uri);
        return $uri;
    }
}