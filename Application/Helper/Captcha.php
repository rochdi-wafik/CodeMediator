<?php

/*
 * Copyright 2022 Rochdi Wafik
 * Version 1.0
 */

/**
 *  Methods
 * -------------------------------------------------------------------------------------------
 * 
 * @method  captcha_create()  : return image [base46-encoded]
 *          You can use this function directly to build random captcha image 
 *          Example: <img src="<?= captcha_create()?>" alt="captcha code">
 * 
 * @method  create_validate($code)  : return boolean
 *          use this function to validate posted captcha code
 *          Example: var_dump( create_validate($_POST['captcha-code']) )
 * Note: On POST : make sure to call captcha_validate() before captcha_create()
 */

/**
 * How to use
 * -----------------------------------------------------------------------------------
 * Create new instance   :  $captcha = new Captcha()
 * Get captcha image      :  $img = $captcha->getImage()
 * Validate captcha code :  $captcha->checkCaptcha($_POST['code'])
 * -----------------------------------------------------------------------------------
 * @var Warning: On POST : make sure to validate posted code before create new captcha
 * 
 * @example 
 *     $obj = new Captcha();
 * 
 *     # validate captcha
 *     if(isset($_POST)){
 *         $obj->checkCaptcha($_POST['code'])
 *     }
 * 
 *     # create captcha 
 *     $this->data['captcha-img'] = $obj->getImage() || $obj->getBase64()
 *     
 */ 

class Captcha{
    /**
     * singleton instance
     */
    private static $INSTANCE;

    /**
     * Captcha Image Width & Height
     */
    public $captcha_width = 150;
    public $captcha_height = 40;

    /**
     * number of characters
     */
    public $captcha_numbers = 5;

    /**
     * Case-insensitive 
     */
    public $case_insensitive = true;

    /**
     * Output base64
     */
    private $output_base64;
    /**
     * Output html
     */
    private $output_html;

    /**
     * Captcha output temp code
     */
    private $captcha_code;

    /**
     * Get Instance
     * ----------------------------------------------------------------
     */
    public static function getInstance(): Captcha{
        if(!self::$INSTANCE){
            self::$INSTANCE = new self();
        }
        return self::$INSTANCE;
    }
    
    /**
     * Create new captcha
     * @return void
     */
    function __construct()
    {
        ob_start();

        $image = imagecreatetruecolor($this->captcha_width, $this->captcha_height);

        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 255, 255);
        $line_color = imagecolorallocate($image, 64, 64, 64);
        $pixel_color = imagecolorallocate($image, 0, 102, 255);

        imagefilledrectangle($image, 0, 0, $this->captcha_width, $this->captcha_height, $background_color);

        for ($i = 0; $i < 3; $i++) {
            imageline($image, 0, rand() % 50, $this->captcha_width, rand() % $this->captcha_height, $line_color);
        }

        for ($i = 0; $i < 1000; $i++) {
            imagesetpixel($image, rand() % $this->captcha_width, rand() % $this->captcha_height, $pixel_color);
        }


        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
        $len = strlen($letters);
        $letter = $letters[rand(0, $len - 1)];

        $text_color = imagecolorallocate($image, 0, 0, 0);
        $word = "";
        for ($i = 0; $i < $this->captcha_numbers; $i++) {
            $letter = $letters[rand(0, $len - 1)];
            imagestring($image, 15, 5 + ($i * 30), 20, $letter, $text_color);
            $word .= $letter;
        }

        // save captcha code
        $this->captcha_code = $word;

        // set path where to save temp output image
        $outputPath = BASE.DS.mt_rand().'.png';
        imagepng($image, $outputPath);

        // save output image
        $this->output_base64 = base64_encode(file_get_contents($outputPath));
        $this->output_html = "<img src='data:image/png;base64,{$this->output_base64}' alt='captcha challenge'>";
        
        // destroy temp output
        imagedestroy($image);
        @unlink($outputPath);
        ob_flush();
    }

    /**
     * Return image as base64
     * @return string
     */
    public function getBase64()
    {
        $_SESSION['captcha-code'] = $this->captcha_code;
        return "data:image/png;base64,".$this->output_base64;
    }

    /**
     * Return image as tag <img> 
     * @return string
     */
    public function getImage()
    {
        $_SESSION['captcha-code'] = $this->captcha_code;
        return $this->output_html;
    }


    /**
     * Validate posted captcha code
     * @return boolean 
     */
    public function validate($posted_code)
    {
        // check case sensitive before validate
        if($this->case_insensitive !== false)
        {
            $result = (strtolower($_SESSION['captcha-code']) === strtolower($posted_code));
        }
        else{
            $result = ($_SESSION['captcha-code'] === $posted_code);
        }

        // return validation result
        if($result)
        {
            unset($_SESSION['captcha-code']);
            return true;
        }
        unset($_SESSION['captcha-code']);
        return false;
    }
}