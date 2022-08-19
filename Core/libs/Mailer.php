<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require_once __DIR__.'/bin/PhpMailer/Exception.php';
require_once __DIR__.'/bin/PhpMailer/PHPMailer.php';
require_once __DIR__.'/bin/PhpMailer/SMTP.php';


class Mailer
{
    private $Host;
	private $SMTPAuth ;
    private $Username;
	private $Password ;
    private $Port ;

    public function __construct($host,$smtp_auth,$username,$password,$port)
	{
		$this->Host = $host;
        $this->SMTPAuth = $smtp_auth;
        $this->Username = $username;
        $this->Password = $password;
        $this->Port = $port;
	}

    public  function sendEmail($to, $subject, $text)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->Host;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = $this->SMTPAuth;                                   //Enable SMTP authentication
            $mail->Username   = $this->Username;                     //SMTP username
            $mail->Password   = $this->Password;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = $this->Port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom($this->Username, 'Noreply');
            $mail->addAddress($to);     //Add a recipient
        
            //Content
            $mail->isHTML(false);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $text;
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public  function sendEmailHTML($to, $subject, $html)
    {
        $mail = new PHPMailer(true);
        
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->Host;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = $this->SMTPAuth;                                   //Enable SMTP authentication
            $mail->Username   = $this->Username;                     //SMTP username
            $mail->Password   = $this->Password;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = $this->Port;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom($this->Username, 'Noreply');
            $mail->addAddress($to) ;    //Add a recipient
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->send();
            return  true;
        } catch (Exception $e) {
            return false;
        }
    }

    public  function sendEmailWithAttachment($to, $subject, $body,$hasHTML =false,Array $attachments = [])
    {
        $mail = new PHPMailer(true);
                
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->Host;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = $this->SMTPAuth;                                   //Enable SMTP authentication
            $mail->Username   = $this->Username;                     //SMTP username
            $mail->Password   = $this->Password;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = $this->Port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom($this->Username, 'Noreply');
            $mail->addAddress($to);     //Add a recipient
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            foreach ($attachments as $item) {
                $mail->addAttachment($item);
            }
        
            //Content
            $mail->isHTML($hasHTML);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->send();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }


	
} 

