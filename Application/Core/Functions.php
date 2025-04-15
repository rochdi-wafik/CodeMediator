<?php // Last Edit : 18-09-2022

use Core\URI;

/**
 * 
 * Algorithms 
 *
 * -------------------------------------------------------------
 * csrf protection 
 * -------------------------------------------------------------
 * (Stolling form and use it outside the website)
 * We Need Two Functions :
 *    csrf() to create a form token
 *    csrf_validation() to validate the form token
 *         _____________________________
 * csrf(id = null);
 * [-] Generate unique session token for each form
 * [-] By default session name is 'csrf_token'
 * [-] !But if we have more than form in single page 
 *     => Then we have to make unique session name to avoid duplication
 *     => So we have to assign an id to the csrf() using argument [id]
 *         _____________________________  
 * csrf_validation(id = null) true : false;
 * [-] check posted token if is the same as session token
 * [-] if we have more than form in a single page 
 *     => Then we have to define which csrf by using argument [id]
 *
 * ----------------------------------------------------------------
 * Base Url Workflow : See Doc -> base_url
 */

function get_http()
{
	return URI::getHttp();
}

function has_http_schema($string){
	if(!is_domain_or_url($string)) return false;
	return preg_match('/^([a-zA-Z][a-zA-Z0-9+.-]*):\/\//', $string);
}

function add_http_if_missing($string) {
    if (preg_match('/^([a-zA-Z][a-zA-Z0-9+.-]*):\/\//', $string)) {
        return $string; // scheme is present, return as is
    }
    return get_http() . $string;
}

/**
 * -------------------------------------------------------
 *  Create Url
 * -------------------------------------------------------
 * - User may pass two types: absolute url | relative url
 * [1] if we have absolute url: "example.com/auth/login"
 *    - add http schema if missing and return the url.
 *    -> "https://example.com/auth/login"
 * [2] if we have relative url: "/auth/login"; 
 *    - add the base_url to the given url
 *    -> "https://base_url.com/auth/login";
 */
function create_url($url){
	// [1] absolute url, like: http://xxxx.xx/xx/xx
	if($url!=null && is_domain_or_url($url)){
		// add schema if not isset
		return add_http_if_missing($url);
	}

	// [2] relative url, like: /xxx/xxx
	$url = trim($url, "/");
	return base_url($url);
}


/**
 * -------------------------------------------------------
 * Base Url
 * -------------------------------------------------------
 */
function base_url($add_link = null, $with_protocol=true)
{

	
	// [2] relative link
	$base_url = URI::getBaseUrl();	
	// add link to base_url
	if($add_link != null){
		// remove any first slash (ex: /link)
		$add_link = ltrim($add_link, '/');
		$base_url .= '/'.$add_link;
	}

	if(!$with_protocol){
		$base_url = preg_replace('/^(https?:\/\/)/', '', $base_url);
	}
	return $base_url;
}

/**
 * Return Current Url
 * -------------------------------------------------------
 * Example:
 */
function current_url($filter_query = false)
{
	$url =  get_http().$_SERVER['HTTP_HOST'].'/'.ltrim($_SERVER['REQUEST_URI'],'/');

	if($filter_query == true){
		$url = rtrim($url, parse_url($url, PHP_URL_FRAGMENT));
		$url = rtrim($url,parse_url($url, PHP_URL_QUERY));
		$url = str_replace('?', '',$url);
	}
	return $url;
}

/**
 * Return Current Page Request
 * -------------------------------------------------------
 * Example:
 */
function page_url($request = null)
{
	if($request == null){
	    return get_http().$_SERVER['HTTP_HOST'].'/'.ltrim($_SERVER['REQUEST_URI'],'/');
	}else{
		return get_http().$_SERVER['HTTP_HOST'].'/'.ltrim($_SERVER['REQUEST_URI'],'/').'/'.ltrim($request, '/');
	}
}

/**
 * Check if domain/url or not
 * ---------------------------------------------------------------
 * return true if url including: domain, ip, link.
 * @example
 * is_domain_or_url("http://example.com") => true
 * is_domain_or_url("http://example.com/auth") => true
 * is_domain_or_url("example.com/auth") => true
 * is_domain_or_url("example.com") => true
 * is_domain_or_url("auth/login") => FALSE!
 * 
 */
function is_domain_or_url($string): bool {
    $pattern = '/^(?:https?:\/\/)?(?:(?:[\da-z\.-]+)\.(?:[a-z\.]{2,6})|(?:\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}))(?:[\/\w \.-]*)*\/?$/';
    return preg_match($pattern, $string) ? true : false;
}
/**
 * Is Development Mode
 * -----------------------------------------------------
 * Check if we are in developement mode
 */
function is_devmode()
{
	global $config;

	if(isset($config['devmode']))
	{
		if($config['devmode'] === true)
		{
			return true;
		}
		elseif($config['devmode'] === false){
			return false;
		}
	}
	return null;
}

/**
 * Check If is config
 * ----------------------------------------------------
 * Check if the config entry is exists
 */
function is_config($config_name)
{
	global $config;
	
	return isset($config[$config_name]) ? true : false;
}

/**
 * Get Config Entry
 * ----------------------------------------------------
 * Get config value by its name
 */
function get_config($config_name)
{
	global $config;
	if(isset($config[$config_name]))
	{
		return $config[$config_name];
	}
	return false;
	
}
// Compare a config OR check if config is set (null not supported)
/**
 * Check value in a config variable 
 * --------------------------------------------------------
 * i.e Config: $config['show_welcome'] = false;
 * usage:
 *  if(verify_config('show_welcome) == true){..}
 */
function verify_config($config_key, $config_val = null)
{
	global $config;

	// Check Config
	if($config_val == null){
		return isset($config[$config_key]) ? true : false;
	}

	// Compare Config
	if(isset($config[$config_key]))
	{
		if($config[$config_key] === $config_val)
		{
			return true;
		}
		else{
			return false;
		}
	}
	return;
}

/**
 * Get Default App language
 * -------------------------------------------------
 */
function app_lang()
{
	global $config;
	if(isset($config['language']) AND !empty($config['language']))
	{
		return $config['language'];
	}
	return;
}

/**
 * Redirect 
 * -------------------------------------------------
 */
function redirect($page = '')
{
	header('location:' .$page);
}

/**
 *  Show Custom UI Msg
 * -----------------------------------------------------------
 */
function show_msg($msg = null,  $type = null, $legend = null)
{
	if($msg == null){
		return null;
	}
	
	$border_color = null;
	$bg_color = null;
	
	switch ($type) {
		case SUCCESS:
			$border_color = "#4caf50";
			$bg_color = "#deeade";
			break;
		case WARNING:
			$border_color = "#f3a800";
			$bg_color = "#f3a8002b";
			break;
		case DANGER:
			$border_color = "#dc3545";
			$bg_color = "#f7dfe1";
			break;
		case PRIMARY:
			$border_color = "#007bff";
			$bg_color = "#d7e9fc";
			break;
		case SECONDARY:
			$border_color = "#6c757d";
			$bg_color = "#e7e8e9";
			break;
		case INFO:
			$border_color = "#03a9f4";
			$bg_color = "#daeff2";
			break;
		default:
			$border_color = "#007bff";
			$bg_color = "#007bff25";
			break;
	}
	$direction = (verify_config('language','arabic')) ? 'right' :'left';
	// $direction = is_devmode() ? 'left' : $direction;

	$stylesheet = "
		font-size: 15px;
		color: #555;
		line-height: 28px;
		width: 100%;
		padding: 1rem;
		margin: 0.3rem 0;
		display: flex;
		justify-content: flex-start;
		align-items: center;
		flex-wrap: wrap;
		background-color: {$bg_color};
		border-{$direction}: 3px solid ;
		border-color: {$border_color};

	";
	
	$legend = ($legend != null ) ? $legend.' :' : null;

	/**/
	$div = '<style>html,*{padding:0;margin:0;box-sizing:border-box}.show_msg a:not([href]){color:#0a9adb;padding:0 4px}</style>
	<div class="show_msg" style="'.$stylesheet.'">
	<b style="color:'.$border_color.'">'.$legend.'&nbsp;</b> <p style="margin:0">'.$msg.'</p></div>';

	return $div;

}

// Show codemediator errors
function show_error($content, $title = null, $description = null)
{
	$legend = ($title != null) ? $title : 'CodeMediator Errors';
	$box = "<style>
	            .cm_error *{
					direction: ltr!important;
					text-align:left!important;
				}
				.cm_error fieldset{
					padding: 1rem;
					margin: 1rem;
					border: 2px solid #dc3545;
					background: #fbfbfbad;
				}
				.cm_error legend{
					font-size:1rem;
					padding: 0.5rem;
					color: #fff;
					background: #dc3545;
					font-family: monospace;
					width: fit-content;
				}
				.cm_error .err_content{
					margin:0.5rem 0;
				}
				.cm_error .show_msg{
					border-right:unset!important;
					border-left: 3px solid;
				}
			</style>
			<div class='cm_error'>
			<fieldset>
				<legend>".$legend."</legend>
				<div class='err_content'>{$content}</div>";
			if($description != null):
				$box .= "
					<details>
						<summary style='color:#606060;cursor:pointer'>Click to show details</summary>
						<div>{$description}</div>
					</details>";
			endif;
		$box .= "</fieldset></div>";
			
	return $box;
}

/**
 * show page 404
 * --------------------------------------------------------------
 * @deprecated use page_404();
 */
function page404($echo = false)
{
	$page = "
	<!DOCTYPE html><html><head><title>404</title><style type='text/css'>.p404{margin: 0px;padding: 0px;box-sizing: border-box;font-family: jozoor,janna, sans-serif;outline: none;scroll-behavior: smooth;position: relative;display: flex;width: 100%;height: 100%;justify-content: center;align-items: center;background: #fff!important;}.box404{background: #fff;position: absolute;width: 100%;height: 100%;z-index: 9999;}.box404 .content404{display: flex;justify-content: center;align-items: center;flex-direction: column;font-family: sans-serif;width: 100%;background: #fff;height: 50vh;}.box404 label{color: #444;letter-spacing: 1px;font-size: xx-large;font-weight: bold;margin-bottom: -5px;}</style></head><body class='p404'><div class='box404'><div class='content404'><label>Oops 404</label><p>The page you looking for is not exist </p><a style='text-decoration:none; color:#03a9f4' href='".base_url()."'>home</a></div></div></body></html> ";
	if($echo == true){
		echo $page;
	}
	else{
		return $page;
	}
}

function page_404($echo = false)
{
	$page = "
	<!DOCTYPE html><html><head><title>404</title><style type='text/css'>.p404{margin: 0px;padding: 0px;box-sizing: border-box;font-family: jozoor,janna, sans-serif;outline: none;scroll-behavior: smooth;position: relative;display: flex;width: 100%;height: 100%;justify-content: center;align-items: center;background: #fff!important;}.box404{background: #fff;position: absolute;width: 100%;height: 100%;z-index: 9999;}.box404 .content404{display: flex;justify-content: center;align-items: center;flex-direction: column;font-family: sans-serif;width: 100%;background: #fff;height: 50vh;}.box404 label{color: #444;letter-spacing: 1px;font-size: xx-large;font-weight: bold;margin-bottom: -5px;}</style></head><body class='p404'><div class='box404'><div class='content404'><label>Oops 404</label><p>The page you looking for is not exist </p><a style='text-decoration:none; color:#03a9f4' href='".base_url()."'>home</a></div></div></body></html> ";
	if($echo == true){
		echo $page;
	}
	else{
		return $page;
	}
}

/**
 * Include Layout
 * ------------------------------------------------------
 * include file from layout/ folder
 */
function layout($layout)
{	
    $file =  trim(VIEWS.DS.'layouts'.DS.$layout, '.php').'.php';
    if(file_exists($file)){
        include_once $file;
	}
	else{
        echo is_devmode() ? show_msg("layout $layout Not Found !", DANGER) : null;
    }
}

// upercase and lowercase shurtcut
function upper($str = null)
{
	return strtoupper($str);
}
function lower($str = null)
{
	return strtolower($str);
}

/**
 * Capitilize Specific Letter
 * --------------------------------------------------------
 */
function capitalize($str, $letter = null)
{
	// Remove White Space
	$str = ltrim($str, ' ');
	// Convert To Array
	$arr = str_split($str);
	// Capitalize letter
	if($letter == null){
		$arr[0] = strtoupper($arr[0]);
	}
	else{
		$arr[$letter-1] = strtoupper($arr[$letter-1]);
	}
	// Convert To String
	return implode($arr);
}

// Date Formate | String To Date
function parse_date($str_time, $with_time=null, $pattern=null)
{
	if($pattern!=null){
		return date($pattern, strtotime($str_time));
	}
	if($with_time){
		return date("M j, Y - H:i", strtotime($str_time));
	}
	return date("M j, Y", strtotime($str_time));
}

function format_date(string $pattern, string $date_str, string $format){
	// @todo temp
	if(true){
		return $date_str;
	}
	// @todo fix this
	$date = DateTime::createFromFormat($pattern, $date_str);
    return $date->format($format);
}

function parseBytes($bytes, bool $upper=false, bool $space=false){
	$unit = " mb";
	if(!$space) {
		$unit = trim($unit);
	}
	$result =  $bytes.$unit;
	return ($upper) ? strtoupper($result) : $result;
}

/**
 * Set Cookie
 * ----------------------------------------------------------------
 * @todo date_to_live should be wrapped in a config file
 */
function cookie($name, $value)
{
	setcookie($name, $value, time() + (86400 * 30), "/"); //30 days
}



/*-----------------------------------------------------------------
 * Data Validation | Sanitize Functions |  PHP 5.2.0+
 *------------------------------------------------------------------
 * Validate : Check If Data [?] Or Not => return True or False
 * Sanitize : Filter Data From [?] And Return It 
 *
 * Validate Func - Uses With If Condition
 *               - if(_is_int($int)): 'its int' ? 'its not';
 * Sanitize Func - Output The New Data After Sanitizing
 *               - echo _filter_str($string);
 *
 */

// Validate Email
function is_email($data)
{
	if(!filter_var($data, FILTER_VALIDATE_EMAIL) === false)
	{
		return true;
	}
}

// Validate IP Address
function is_IP($data)
{
	if(!filter_var($data, FILTER_VALIDATE_IP) === false)
	{
		return true;
	}
}

// Validate URL
function is_URL($data)
{
	if(!filter_var($data, FILTER_VALIDATE_URL) === false)
	{
		return true;
	}
}

// Sanitize String - remove tags
function filter_str($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
	return $newData;
	
}

// Sanitize URL : Remove Illegal Characters
function filter_url($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_URL);
	return $newData;
	
}

// Sanitize Magic Quotes : ' and \ 
function filter_quotes($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
	return $newData;

	
}

// Sanitize html charachters : <tag> prevent execute html tags
function filter_html($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
	return $newData;
	
}

// prevent execute html tags
function html_encode($data)
{ 
	return htmlspecialchars($data);
}
// decode html tags
function html_decode($data)
{
	return htmlspecialchars_decode($data);
}

/*
 * Arrays Functions  
 * ---------------------------------------------------------   
 * @todo rename to is_arr_null()                           
 */

// Check Array If It's Values Or One Of Them Are Null
function arr_null($array)
{
	foreach ($array as $key => $value)
	{
		if($value == ''){
			return true;
		}
	}
	return false;
}

// Check Array If One Of String Values Is More Than 
function arr_len($array, $len)
{
	// if one of them less than $len : return false
	foreach ($array as $key => $value)
	{
		if(strlen($value) <= $len){
			return false;
			break;
		}
	}
	return true;
}
// Check Array If One Of String Values Is Less Than 
function arr_less($array, $len)
{
	foreach ($array as $key => $value)
	{
		if(strlen($value) <= $len){
			return true;
			break;
		}
	}
	return false;
}
/*---------/--------------------------------------*/
// Check Request Method
/**
 * Is Post Request
 * ---------------------------------------------------------
 * @todo rename to is_post_request() || is_post_method()
 */
function post_method($post = null)
{
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if($post != null)
		{
			if(!isset($_POST[$post]))
			{
				return false;
			}
		}
		return true;
	}else{
		return false;
	}
}
/**
 * Is GET Request
 * ---------------------------------------------------------
 * @todo rename to is_get_request() || is_get_method()
 */
function get_method($get = null)
{
	if($_SERVER['REQUEST_METHOD'] == 'GET')
	{
		if($get != null)
		{
			if(!isset($_GET[$get]))
			{
				return false;
			}
		}
		return true;
	}else{
		return false;
	}
}

/*-----------------Security------------------*/

// Set Agent Setup
function getUserAgent()
{   
	return $_SERVER['HTTP_USER_AGENT'];
}

function getUserIp()
{
	$ip = null;
	if(!empty($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function prevent_sess_hijacking()
{
	/*----------- Prevent Session Hijacking ------------*/
	# get user ip
	$ip = null;
	if(!empty(@$_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = @$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
    else{
		$ip = @$_SERVER['REMOTE_ADDR'];
	}
	# get user agent
    $agent =  @$_SERVER['HTTP_USER_AGENT'];
    /**/
	@session_start();
	// if first visit, set a code verification [ip+agent]
	if (empty($_SESSION['userInfo']))
	{
		// PHP7 @session_unset($_SESSION['userInfo']);
		unset($_SESSION['userInfo']);
		$_SESSION['userInfo'] = md5($agent.$ip);
	}
	// if not, check if code is the same as we set before
	if($_SESSION['userInfo'] != md5($agent.$ip))
	{
		// if not the same, destroy session
		session_unset();
		session_destroy();
	}

}
/*-----------------Csrf--------------------------------*/
/**
 * Create CSRF Token
 * --------------------------------------------------------------
 * @deprecated use the CsrfHelper class
 */
function csrf($id = null)
{
	@session_start();
	$sess_name = ($id != null) ? 'csrf-token_'.$id : 'csrf-token';

	$_SESSION[$sess_name] = md5(uniqid(mt_rand(),true));
	return "<input type='hidden' name='csrf-token' value='".$_SESSION[$sess_name]."' >"; 
	 
}

/**
 * Validate CSRF Token
 * --------------------------------------------------------------
 * @deprecated use the CsrfHelper class
 */
function csrf_validation($id = null)
{
	@session_start();
	$sess_name = ($id != null) ? 'csrf-token_'.$id : 'csrf-token';

	if(isset($_POST['csrf-token']))
	{
		if($_POST['csrf-token'] == @$_SESSION[$sess_name])
		{
			$_SESSION[$sess_name] = null;
			unset($_SESSION[$sess_name]);
			return true;
		}
	}
	return false;
}
/**/

function clear_sess_dir($path = null)
{
	if($path == null){
		global $config;
		if(isset($config['sess-dir']) AND !empty($config['sess-dir']))
		{
			$path = $config['sess-dir'];
	    }
	}
	foreach (scandir($path) as $file)
	{
		if(is_file($path.DS.$file))
		{
			if(filesize($path.DS.$file) == 0){
		        @unlink($path.DS.$file);// Delete file if empty
			}
		}
	}

}

// New : 24-06-2022

// Flash Messages
/**
 * Set Temp Message
 * ---------------------------------------------------------------------------
 * - Store temp message in cache (session)
 * - Rename from setFlashMsg() to setTempMsg()
 */
function set_flash_msg($msg = null, $id = null)
{
	@session_start();
	$sess_name = ($id != null) ? 'flash_msg_'.$id : 'flash_msg';
	$_SESSION[$sess_name] = $msg;
}
function set_temp_msg($msg = null, $id = null)
{
	@session_start();
	$sess_name = ($id != null) ? 'flash_msg_'.$id : 'flash_msg';
	$_SESSION[$sess_name] = $msg;
}

/**
 * Get Temp Message
 * ---------------------------------------------------------------------------
 * - Once message are retrieved, remove it from cache (session)
 * - Rename
 *  from getFlashMsg() to getTempMsg()
 */
function get_flash_msg($id = null)
{
	@session_start();
	$sess_name = ($id != null) ? 'flash_msg_'.$id : 'flash_msg';
	if(isset($_SESSION[$sess_name]) AND $_SESSION[$sess_name] != null)
	{
		echo $_SESSION[$sess_name];
		$_SESSION[$sess_name] = null;
		unset($_SESSION[$sess_name]);
	}
}
function get_temp_msg($id = null)
{
	@session_start();
	$sess_name = ($id != null) ? 'flash_msg_'.$id : 'flash_msg';
	if(isset($_SESSION[$sess_name]) AND $_SESSION[$sess_name] != null)
	{
		echo $_SESSION[$sess_name];
		$_SESSION[$sess_name] = null;
		unset($_SESSION[$sess_name]);
	}
}

// Add on : 28-6-2022
function load($file, $method = 'require'){
	$file = str_replace('/',DS,$file);
	$file = trim($file,'.php').'.php';
	$file = APP.DS.$file;

	if(file_exists($file))
	{
		if($method == 'require') { require $file;} 
		elseif($method == 'include') { include $file ;}
		elseif($method == 'require_once') { require_once $file;}
		elseif($method == 'include_once') { include_once $file;}
	}
	else{
		echo is_devmode() ? show_msg('file <u>'.$file.'</u> is not exists' ,WARNING): null;
		return false;
	}
}

// Add on: 17-7-2022
function hash_input_filename($file = null, $merge = null){
	if($file == null)
	{
		return uniqid(mt_rand(),true); 
	}
	$filename   = pathinfo($_FILES[$file]['name'], PATHINFO_FILENAME);
	$file_ext   = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
	return $merge.'_'.md5($filename).'.'.$file_ext;
}

// Add on: 23-08-2022
function remove_json_comments($json)
{
	//Strip multi-line comments: '/* comment */'
	$json = preg_replace('!/\*.*?\*/!s', '', $json); 
	//Strip single-line comments: '// comment'
	$json = preg_replace('!//.*!', '', $json); 
	//Remove empty-lines (as clean up for above)     
	$json = preg_replace('/\n\s*\n/', "\n", $json); 
	return $json;
}

/**
 * Encrypting - Decryptiong
 * ---------------------------------------------------------
 * Update this functions to work with old and new php verion
 */

function encrypt($data,$key='YOUR_KEY')
{
	return base64_encode($data);
	return openssl_encrypt($data,"AES-128-CBC",$key);
}
function decrypt($data,$key='YOUR_KEY')
{
	return base64_decode($data);
	return openssl_decrypt($data,"AES-128-CBC",$key);
}

/**
 * Captcha Callenge
 * -------------------------------------------------------------------
 * Create & Validate Captcha Challenge
 * @deprecated new captcha methods are weapped in Captcha.php class
 */

/**
 * @method captcha_create()
 * @return image src as to base64
 * @example 1 <img src="<?= captcha_create()?>" alt="captcha code">
 * @example 2 $this->data["img_src"] = captcha_create();
 */
function captcha_create()
{
	$captcha_width = 150;
    $captcha_height = 40;
    $captcha_numbers = 5;
    $output_base64='';
    $captcha_code='';

	ob_start();
	$image = imagecreatetruecolor($captcha_width, $captcha_height);

	$background_color = imagecolorallocate($image, 255, 255, 255);
	$text_color = imagecolorallocate($image, 0, 255, 255);
	$line_color = imagecolorallocate($image, 64, 64, 64);
	$pixel_color = imagecolorallocate($image, 0, 102, 255);

	imagefilledrectangle($image, 0, 0, $captcha_width, $captcha_height, $background_color);

	for ($i = 0; $i < 3; $i++) {
		imageline($image, 0, rand() % 50, $captcha_width, rand() % $captcha_height, $line_color);
	}

	for ($i = 0; $i < 1000; $i++) {
		imagesetpixel($image, rand() % $captcha_width, rand() % $captcha_height, $pixel_color);
	}


	$letters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
	$len = strlen($letters);
	$letter = $letters[rand(0, $len - 1)];

	$text_color = imagecolorallocate($image, 0, 0, 0);
	$word = "";
	for ($i = 0; $i < $captcha_numbers; $i++) {
		$letter = $letters[rand(0, $len - 1)];
		imagestring($image, 15, 5 + ($i * 30), 20, $letter, $text_color);
		$word .= $letter;
	}

	// save captcha code
	$captcha_code = $word;

	// set path where to save temp output image
	$outputPath = BASE.DS.mt_rand().'.png';
	imagepng($image, $outputPath);

	// save output image
	$output_base64 = base64_encode(file_get_contents($outputPath));
	
	// destroy temp output
	imagedestroy($image);
	@unlink($outputPath);
	ob_flush();

	// save code to be compared
	@session_start();
	$_SESSION['captcha_code'] = $captcha_code;
	return "data:image/png;base64,".$output_base64;
}

/**
 * Validate Captcha
 * --------------------------------------------------------------------------
 * @param bollean $case_insensitive
 * @param mix $_POST posted captcha code
 * @return boolean 
 * 
* @deprecated new captcha methods are weapped in Captcha.php class
 */
function captcha_validate($posted_code, $case_insensitive = true)
{
	// check case sensitive before validate
	if($case_insensitive)
	{
		$result = (strtolower($_SESSION['captcha_code']) === strtolower($posted_code));
	}
	else{
		$result = ($_SESSION['captcha_code'] === $posted_code);
	}

	// return validation result
	if($result)
	{
		unset($_SESSION['captcha_code']);
		return true;
	}
	unset($_SESSION['captcha_code']);
	return false;
}


/**
 * Trace route  (Html)
 * ---------------------------------------------------------------------
 * When this function is called
 * It will print all classes & mathods that is called untill the point where tracerout() is called
 * @param $should_print: if true: echo output. if false: return output
 * 
 * @todo wrap this method in a Logger class
 */
function tracerout($should_print = true){
	if ( is_devmode() ) {
		// Get the backtrace
		$backtrace = debug_backtrace();
		$div_open = "<div class='tracebox'><fieldset><legend style='color:#ffff'>Tracerout</legend><div class='content'>";
		$output="";
	  
		// Loop through the backtrace elements
		foreach ( $backtrace as $i => $trace ) {

		if($trace['function'] != 'tracerout'){ // dont show last function which is traceroute() itself
		  // Get the function name
		  if ( isset( $trace['class'] ) ) {
			$output = "<p><span>[=]</span> <label class='class'>{$trace['class']}{$trace['type']}</label><label class='function'>{$trace['function']}()</label></p>".$output;
		  } else {
			$output = "<p><span>[=]</span> <label class='function'>{$trace['function']}()</label></p>".$output;
		  }
		}
	  
		  // Stop after reaching the first script file
		  if ( isset( $trace['file'] ) && strpos( $trace['file'], $_SERVER['SCRIPT_FILENAME'] ) !== false ) {
			break;
		  }
		}
		$div_close = "</div></fieldset></div>";
	  
		// Display the output
		$style =  "<style>.tracebox{background:#141418;margin:3px 0;border-left:3px solid #c92333; font-family:monospace}.tracebox fieldset{background:inherit!important;padding:5px!important; border:none!important;}.tracebox legend{ color:#fff!important;background:#27223a!important;padding:5px!important}.tracebox .content{display:flex!important;flex-wrap:wrap;}.tracebox p{margin-right:20px; margin:5px;color:#181818}.tracebox span{color:#c33b54}.tracebox .class{color:#22a9dd}.tracebox .function{color: #24a115}</style>";
		$result = $style.$div_open.$output.$div_close;
		if($should_print){
			echo $result;
		}else{
			return $result;
		}
	}
}
/**
 * TraceRout (Log)
 * --------------------------------------------------------------------
 * This method like tracerout(), but it print pure text without html
 * 
 * @todo wrap this method in a Logger class
 */
function tracerout_pure($should_print = true){
		// Get the backtrace
		$backtrace = debug_backtrace();
		$output="";
	  
		// Loop through the backtrace elements
		foreach ( $backtrace as $i => $trace ) {

		if($trace['function'] != 'tracerout_pure'){ // dont show last function which is traceroute() itself
		  // Get the function name
		  if ( isset( $trace['class'] ) ) {
			$output = "[=]{$trace['class']}{$trace['type']} {$trace['function']}()\t".$output;
		  } else {
			$output = "[=]{$trace['function']}()\t".$output;
		  }
		}
	  
		  // Stop after reaching the first script file
		  if ( isset( $trace['file'] ) && strpos( $trace['file'], $_SERVER['SCRIPT_FILENAME'] ) !== false ) {
			break;
		  }
		}

		if($should_print){
			echo $output;
		}else{
			return $output;
		}

}

/**
 * --------------------------------------------------------------------
 * Cut from the end
 * --------------------------------------------------------------------
 * @example
 * $original = "indexController";
 * $cut_me = "Controller"
 * $result = cut_from_end($original, $cut_me); 
 * - output: "index"
 * - IF not found: return the original
 */
function cut_from_end($original, $chars, bool $case_sensitive = false){
	if(!$case_sensitive){
		$original = strtolower($original);
		$chars = strtolower($chars);
	}
	$lastPos = strrpos($original, $chars);
	if ($lastPos !== false) {
		return substr($original, 0, $lastPos);
	} else {
		return $original;
	}
}


// 01-11-2024
function str_end_with($haystack,$needle) {
	//str_starts_with(string $haystack, string $needle): bool

	$strlen_needle = mb_strlen($needle);
	if(mb_substr($haystack,-$strlen_needle,$strlen_needle)==$needle) {
		return true;
	}
	return false;
}

// 07-11-2024
function create_slug($text){
	$text = preg_replace('/[^a-zA-Z0-9]/', '-', $text); // replace non-alphanumeric characters with underscore
	$text = trim($text, '_'); // remove leading and trailing underscores
	$text = strtolower($text); // convert to lowercase
	return $text;
}

// 08-11-2024
function array_keys_exists($required_keys, $original_array){
	$missing_keys = array_diff_key(array_flip($required_keys), $original_array);

	if (empty($missing_keys)) {
		// All required keys exist in the array
		return true;
	} else {
		// The following keys are missing: " . implode(", ", array_keys($missing_keys));
		return false;
	}
}

function array_get_missing_keys($required_keys, $original_array){
	$missing_keys = array_diff_key(array_flip($required_keys), $original_array);

	if (empty($missing_keys)) {
		// All required keys exist in the array
		return null;
	} else {
		return array_keys($missing_keys);
	}
}


/**
 * ---------------------------------------
 *  Is Image Url | 12-11-2024
 * ----------------------------------------
 * 
 */
function is_img_url($string): bool{
	if(!isset($string) || $string==null ) return false;
	$allowedExtensions = array('jpg','ico', 'svg','webp', 'jpeg', 'png', 'gif', 'bmp', 'tiff');
	$extension = pathinfo($string, PATHINFO_EXTENSION);
	return in_array($extension, $allowedExtensions);
}



/**
 * --------------------------------------------------------------------------
 * Is Blank
 * --------------------------------------------------------------------------
 * - What's wrong with empty()?
 * - Function empty handle `0` and `false` as empty value.
 * - Empty return true also if the value is: (0, 0.0, "0", false).
 * - But sometimes we only return true if value is null or blank.
 * --------------------------------------------------------------------------
 * @example: Lets say we want to filter empty json data sent by client.
 * - We first convert this data to array, then we use array_filter()
 * - But if user sent json with value false or 0, this will be filtered too.
 * - We don't want this, because user may need to use values 0 or false in json,
 *   I.e update user status: So json will looks like this: {status: 0}
 * - In this case, we can use is_blank() as a callback to filter_data()
 *   $data = filter_data($data, function($value){
 *         // if value is not null or empty, keep it
 *         return !is_bank($value);
 *   });
 */
function is_blank($value){
	if($value!==null && is_string($value)) $value = trim($value);
	return  ($value===null) || ($value==="") ? true : false;
}


/**
 * --------------------------------------------------------------------------
 * Escape html special character from any type
 * --------------------------------------------------------------------------
 * - apply htmlspecialchars to any Data Type: String, Array, or Object
 */
function htmlspecialchars_any($value) {
    if (is_string($value)) {
        return htmlspecialchars($value);
    } 
	elseif (is_array($value)) {
        return array_map('htmlspecialchars_any', $value);
    } 
	elseif (is_object($value)) {
        $escapedObject = clone $value;
        foreach (get_object_vars($escapedObject) as $key => $val) {
            $escapedObject->$key = htmlspecialchars_any($val);
        }
        return $escapedObject;
    } else {
        return $value;
    }
}


/**
 * --------------------------------------------------------------------------
 * Trim any type
 * --------------------------------------------------------------------------
 * - apply trim to any Data Type: String, Array, or Object
 */
function trim_any($value) {
    if (is_string($value)) {
        return trim($value);
    } 
	elseif (is_array($value)) {
        return array_map('trim_any', $value);
    } 
	elseif (is_object($value)) {
        $escapedObject = clone $value;
        foreach (get_object_vars($escapedObject) as $key => $val) {
            $escapedObject->$key = trim_any($val);
        }
        return $escapedObject;
    } else {
        return $value;
    }
}


/**
 * Custom Error Handler
 * --------------------------------------------------------------------
 * - This error handler convert thrown errors to json format.
 * 
 */
// Define a custom error handler
function custom_exception_handler($exception) {
    // Prepare the error response in JSON format
	$response = [
		'status' => 500,
		'code' => $exception->getCode(), // integer
		'error' => array(
			'message' => $exception->getMessage(), // object | null
			'file' => $exception->getFile(),
			'line' => $exception->getLine()
		)
		
	];
    


    // Output the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
	exit;

}
function custom_error_handler($code, $message, $file, $line, $trace) {
    // Prepare the error response in JSON format
	$response = [
		'status' => 500,
		'code' => $code, // integer
		'error' => array(
			'message' => $message,// object || null
			'file' => $file,
			'line' => $line
		)
	];
    

    // Output the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
	exit;

}



/**
 * ==============================================================================
 * Get HTTP Bearer Token
 * ==============================================================================
 * Get Bearer Token From HTTP Authorization
 * 
 * - As the HTTP_AUTHORIZATION key is not always present in the $_SERVER super-global array,
 * - We can use the apache_request_headers() function to get all headers.
 * - This is less-chance needed, as the above function is enough.
 */
function getBearerToken(){
	$headers = [];
	if (function_exists('apache_request_headers')) {
		$headers = apache_request_headers();
	} elseif (function_exists('getallheaders')) {
		$headers = getallheaders();
	} else {
		$headers = $_SERVER;
	}

	if (isset($headers['Authorization']) && preg_match('/Bearer\s+(\S+)/', $headers['Authorization'], $matches)) {
		return $matches[1];
	}
	return null;
}


/**
 * ------------------------------------------------------------------------
 * Check If Password Strong
 * ------------------------------------------------------------------------
 * @return mixed : array or boolean
 * - IF password not strong, method will array contains error details.
 * - IF valid: method will return true.
 * @example
 * if(is_array(($result = is_strong_password("abc123))){
 *    echo $result['message'];
 * }else{
 *    echo "password is valid";
 * }
 */
function is_strong_password(string $password, bool $upper_letters = false,  $min_length=null){
	// Check if the password contains at least one letter
	if (!preg_match('/[a-zA-Z]/', $password)) {
	   return ['code' => 1, 'message' => 'the password must contains at least one letter'];
	}

	// Check if the password contains at least one digit
	if (!preg_match('/[0-9]/', $password)) {
		return ['code' => 2, 'message' => 'the password must contains at least one digit'];
	}

	// Check for minimum length if specified
    if ($min_length !== null && strlen($password) < $min_length) {
		return ['code' => 3, 'message' => 'password must contains at least '.$min_length.' characters'];
    }

    // Check for uppercase letter if required
    if ($upper_letters && !preg_match('/[A-Z]/', $password)) {
		return ['code' => 4, 'message' => 'password must contains at least one uppercase letter'];
    }

    return true;
}


/**
 * -------------------------------------------------------------------------
 * get Last Index Of Array
 * -------------------------------------------------------------------------
 * - Alternative to array_key_last() which is not supported in older php
 */
function array_last_index(array $array): ?int{
	$size = count($array);
    return $size > 0 ? $size - 1 : null;
}

/**
 * Array Map For Assoc
 */
function array_map_assoc($callback, array $arr){
	return array_combine(
		array_map($callback, array_keys($arr)), 
		array_values($arr)
	);
}



/**
 * *****************************************************************************
 * Compare Two Arrays
 * *****************************************************************************
 * - Compare two arrays to check if they match each others.
 * - sorting is taken in consideration by default.
 * -----------------------------------------------------------------------------
 * @example
 *   arrays_equal(['a', 'b', 'c'], ['a', 'b']); // Output: false
 *   arrays_equal(['a', 'b', 'c'], ['a', 'b', 'e']); // Output: false
 *   arrays_equal(['a', 'b', 'c'], ['a', 'b', 'c']); // Output: true
 * -----------------------------------------------------------------------------
 * - IF we don't have to match the sorting two, we can assign true:
 * @example
 *   // Compare without sorting
 *   arrays_equal(['a', 'b', 'c'], ['c', 'a', 'b'], false); // Output: bool(false) 
 *   // Compare with sorting
 *   arrays_equal(['a', 'b', 'c'], ['c', 'a', 'b'], true); // Output: bool(true) 
 */
function arrays_equal(array $arr1, array $arr2, bool $sort_before_compare=false) {
	// Check if both arrays have the same number of elements
	if (count($arr1) !== count($arr2)) {
		return false;
	}
	
	if($sort_before_compare){
		// Sort both arrays for consistent comparison
		sort($arr1);
		sort($arr2);
	}
  
	// Compare elements directly without sorting
	for ($i = 0; $i < count($arr1); $i++) {
		if ($arr1[$i] !== $arr2[$i]) {
			return false;
		}
	}
  
	return true;
}


/**
 * -----------------------------------------------------------------------------
 * Get Directory Files
 * -----------------------------------------------------------------------------
 * List all files in directory path.
 * @param dir_path the absolute path of the target directory.
 * @param scan_sub_dirs scan also files in sub directories of provided directory.
 * @return array contains absolute files paths. 
 * - Example array("C:\apache\www\site\uploads\bar.jpg")
 * - These returned paths can be used to perform file system functions like:
 *   basename(), filesize(), readfile(), etc.
 * 
 */
function get_dir_files($dir_path, $scan_sub_dirs=true):array{
	// get directory handler
	$dir_handler = opendir($dir_path);
	$result = array();

	if ($dir_handler) {
		// check if directory is readable
	    while (($file = readdir($dir_handler)) !== false) {
			// skip fake dirs that represent the current path or parent path
			if ($file != "." && $file != "..") {
				$current_path = $dir_path.DS.$file;
				// if this is subdir, list his files
				if (is_dir($current_path)) {
					// here we can use sub array before scan, since this is subdir
					if($scan_sub_dirs){
						$result = array_merge($result, get_dir_files($current_path));
					}
				} 
				// if this is file, add it to result array
				else {
					$result[] = $current_path;
				}
			}
	    }
		// close directory handler
	    closedir($dir_handler);
	}
	return $result;
}

/**
 * -----------------------------------------------------------------------------
 * List Directory Files
 * -----------------------------------------------------------------------------
 * List all files in directory path.
 * @param dir_path the absolute path of the target directory.
 * @param scan_sub_dirs scan also files in sub directories of provided directory
 * @return array contains absolute files paths. 
 * - Example array("C:\apache\www\site\uploads\bar.jpg")
 * - These returned paths can be used to perform file system functions like:
 *   basename(), filesize(), readfile(), etc.
 * -----------------------------------------------------------------------------
 * @deprecated use get_dir_files() instead of list_dir_files()
 * - This function uses scandir(), which less efficient that readdir() that is
 * used by get_dir_files()
 */
function list_dir_files($dir_path, $scan_sub_dirs=true):array{
	$result = array();
	$files = scandir($dir_path);

	// skip fake dirs that represent the current path or parent path
	unset($files[array_search('.', $files, true)]);
	unset($files[array_search('..', $files, true)]);

	// prevent empty ordered elements
	if (count($files) < 1) return [];

	foreach($files as $file){
		$current_path = $dir_path.DS.$file;
		// if this is subdir, list his files
		if(is_dir($current_path)){
			// here we can use sub array before scan, since this is subdir
			if($scan_sub_dirs){
				list_dir_files($current_path);
			}
			
		}
		// if this is file, add it to result array
		else{
			$result[] = $current_path;
		}
	}

	return $result;
}  


/**
 * Convert Size To Human Readable Size
 * -------------------------------------------------------------
 */
function get_readable_size($bytes, $decimals = 2) {
	// under kb? return bytes
    if ($bytes < 1024) return $bytes . ' B';

    $factor = floor(log($bytes, 1024));
    $size = $bytes / pow(1024, $factor);

    // Format the size with the specified number of decimals
    $formatted_size = sprintf("%." . $decimals . "f", $size);

    // Remove 0 float if found, i.e: 23.0 to 23
    if (substr($formatted_size, -2) == ".0") {
        $formatted_size = substr($formatted_size, 0, -2);
    }

    return $formatted_size . ['B', 'KB', 'MB', 'GB', 'TB', 'PB'][$factor];
}


// IS Associative Array
function is_assoc_array($arr){
	if(is_array($arr)){
		if(array_keys($arr) !== range(0, count($arr) - 1)){
			return true;
		}
	}
	return false;
}

// IS Subsequent Array (List)
function is_list($arr){
	if(is_array($arr)){
		return !is_assoc_array($arr);
	}
	return false;
}

// 25-02-2025
/**
 * Check if current URL is Base URL
 */
function is_home($filter_queries=true): bool{
	$currentUrl = strtolower(trim(current_url($filter_queries), '/'));
	$baseUrl = strtolower(trim(base_url(), '/'));
	return $currentUrl==$baseUrl;
}