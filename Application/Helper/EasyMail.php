<?php

use Core\CodeMediator;

/**
 * Use this class if PHPMailer not supported 
 * This class uses the native mail() function
 * Attachement not supported in v1.0
 * @version 1.0 
 */

class EasyMail
{
    public $from_email; // transmitter name
    public $from_name; // transmitter email
    public $reply_to; // email which receiver can reply

    public $to_email; // receiver email
    public $to_name; // receiver name

    public $subject;
    public $body; // html by default

    /**
     * @var cc-bcc can be one (string) or multiple (array)
     * @example "[user_1@mail.com,user_2@mail.com,user_3@mail.com]"
     */
    public $cc; 
    public $bcc;

    // additional headers
    public $headers;

    function send()
    {
        // format message to html
        $this->headers .= "MIME-Version: 1.0\r\n";
        $this->headers .= "Content-type: text/html; charset=utf-8\r\n";

        // add transmitter
        if(isset($this->from_name))
        {
            $this->headers .= "FROM: {$this->from_name} <{$this->from_email}>\r\n";
        }else{
            $this->headers .= "FROM: {$this->from_email}\r\n";
        }
        
        // add email to reply
        if(isset($this->reply_to)){
        $this->headers .= "Reply-To: {$this->reply_to}\r\n";
        }

        // add cc receivers
        if(isset($this->cc))
        {
            if(is_string($this->cc))
            {
                $this->headers .= "CC: {$this->cc}\r\n";
            }
            if(is_array($this->cc))
            {
                $cc = rtrim(implode(",",(array)$this->cc),",");
                $this->headers .= "CC: {$cc}\r\n";
            }
        }

        // add bcc receivers
        if(isset($this->bcc))
        {
            // add bcc
            if(is_string($this->bcc))
            {
                $this->headers .= "BCC: {$this->bcc}\r\n";
            }
            if(is_array($this->bcc))
            {
                $bcc = rtrim(implode(",",(array)$this->bcc),",");
                $this->headers .= "BCC: {$bcc}\r\n";
            }
        }

        if(!mail($this->to_email,$this->subject,$this->body,$this->headers))
        {
            CodeMediator::show_error(array(
                "title" => "Mailer Exception",
                "content" => "failed to send email",
                "track" => "Helper/EasyMail{} send()"
            ));
            return false;
        }
        return true;

    }


}
