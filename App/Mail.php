<?php
 
namespace App;
 
use \App\Config;
 
//"phpmailer/phpmailer": "~6.0",
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
// Load Composer's autoloader
require_once '../vendor/autoload.php';
 
/**
 * Mail
 *
 * PHP version 7.0
 */
class Mail
{
	public static function send($to, $subject, $text, $html)
	{
		$mail = new PHPMailer(TRUE);
                   
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->Host = Config::SMTP_HOST;
			$mail->Username = Config::SMTP_USER;
			$mail->Password = Config::SMTP_PASS;
			$mail->SMTPSecure = 'ssl';                     
			$mail->Port = 465;
			$mail->setFrom(Config::EMAIL_FROM, "Budżet Manager");
			$mail->addAddress($_POST['email']);
			$mail->CharSet = "UTF-8";
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body = $html;
			$mail->AltBody = $text;
			
			$mail->addAttachment('img/oszczedzanie.png');  
			
			if ($mail->send()) {
				return true;
			} else {
				echo 'Mailer error: ' . $mail->ErrorInfo;
				return false;
			}
			
	}
}