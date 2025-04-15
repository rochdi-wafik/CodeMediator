<?php
/**
 * CSRF 
 * 
 * ---------------------------------------------------------------------------
 * What is CSRF ?
 * ---------------------------------------------------------------------------
 * - Csrf let the attacker to use html form outside the website, the attacker
 *   Copying the html form and place it somewhere outside the website, 
 *   So the request is sent to the server from none-official source.
 * - This can be used for malicious things
 * 
 * ---------------------------------------------------------------------------
 * How to protect from CSRF ?
 * ---------------------------------------------------------------------------
 * - To make sure that the form is sent from our website, none from outside,
 *   We generate a unique token, this token is temp saved in server, and
 *   it must be sent withing the form values, then the server compare the
 *   form token with saved token, if match, means the form is from our website.
 * - IF the attacker is tried to copy the form and send the request from outside,
 *   He must send the token, but the token he copied will be already used by
 *   the original form. 
 * - So this class generate new token for each request, the token valid for one request
 * - When the class validate the token, it will be destroyed
 *   So that the attacker can't use it.
 * 
 * ---------------------------------------------------------------------------
 * How to use this class ?
 * ---------------------------------------------------------------------------
 * - When we create an html form, we obtain a new csrf token from this class,
 *  The token then send withing the other form values, which means, it must be 
 *  Included inside the form.
 * - We have two options to create token inside form:
 * [1] Directly render html input contains the token
 * - Here we don't have to create the input tag, as it will be auto generated,
 * - The output will be: <input type='hidden' name='csrf-token' value='xxxx'/>;
 * [2] Create html input and Obtain token value 
 * - Here we create the input by ourself, and we put the token value.
 * - But make sure to write the correct input name  'csrf-token' 
 *   Get token name: CsrfHelper::TOKEN_NAME |OR| CsrfHelper::getTokenName()
 * - The default input name is: 'csrf-token', but we can change it from the class
 * 
 * ---------------------------------------------------------------------------
 * Example:
 * ---------------------------------------------------------------------------
 * [-] Create Token:
 * 
 * $  <form ....>
 * $     <input name='<?= CsrfHelper::TOKEN_NAME?>' value='<?= CsrfHelper::csrf('csrf_signup', false)?>'
 * $     OR
 * $     <?= CsrfHelper::csrf('csrf_signup') ?>
 * $     OR
 * $     <?= CsrfHelper::create('csrf_signup') ?>
 * $  </form>
 * 
 * [-] Validate Token
 * - When token received, validate it first before process other form values
 * - IF we add id to above method, we add the same in the validation:
 * $  if(CsrfHelper::validate('csrf_signup')): // token is valid
 * 
 */
final class CsrfHelper{
    public const TOKEN_NAME = 'csrf-token'; // <input name='csrf-token' ../>

    /**
     * Create Csrf Token
     * --------------------------------------------------------------------------
     * @param string id: if we have multiple forms (ex: login, signup), set id.
     * @param bool as_html if true, return html tag contain the token.
     * 
     * @return token
     */
    public static function csrf(?string $id = null, bool $as_value = false){
        @session_start();
        $token_session = ($id != null) ? self::TOKEN_NAME.'_'.$id : self::TOKEN_NAME;
        $_SESSION[$token_session] = md5(uniqid(mt_rand(),true));

        // Return ready html tag
        if($as_value){
            return $_SESSION[$token_session];
        }
        // Return value only
        return "<input type='hidden' name='".self::TOKEN_NAME."' value='".$_SESSION[$token_session]."' >"; 

        
    }

    /**
     * Create Token Token
     * --------------------------------------------------------------------
     * - This is just an alias of csrf()
     * @return token
     */
    public static function create($id = null){
        return self::csrf($id);
    }

    /**
     * Validate Token
     * ----------------------------------------------------------------------
     * @param $id: if id used in obtain(), then use the same in this method
     * @return bool true if valid
     */
    public static function validate(?string $id=null){
        @session_start();
        $token_session = ($id != null) ? self::TOKEN_NAME.'_'.$id : self::TOKEN_NAME;
    
        if(isset($_POST[self::TOKEN_NAME])){
            if($_POST[self::TOKEN_NAME] == @$_SESSION[$token_session]){
                $_SESSION[$token_session] = null;
                unset($_SESSION[$token_session]);
                return true;
            }
        }
        return false;
    }


    /**
     * Get Token Name
     * ---------------------------------------------------------------
     * @usage  <input name='<?= CsrfHelper::getTokenName()?>' ../>
     * @return string token name
     */
    public static function getTokenName(){
        return self::TOKEN_NAME;
    }


}
