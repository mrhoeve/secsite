<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './includes/PHPMailer/Exception.php';
require './includes/PHPMailer/PHPMailer.php';
require './includes/PHPMailer/SMTP.php';

include_once "includes/setting.php";

$smtpPortSetting = getSettingValue("smtp_port");
$smtpPort = empty($smtpPortSetting) ? 1125 : (int) $smtpPortSetting;
echo "Port: " . $smtpPort;

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug   = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host        = 'localhost';                    // Set the SMTP server to send through
    $mail->SMTPAuth    = false;                                   // Enable SMTP authentication
    $mail->SMTPAutoTLS =false;
    $mail->Port        = $smtpPort;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    $mail->addAddress('ellen@example.com');               // Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}