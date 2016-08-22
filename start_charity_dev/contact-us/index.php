<?php
require '../src/php/lib/PHPMailer/PHPMailerAutoload.php';

mb_internal_encoding('UTF-8');

function encoding($msg) {
	return mb_encode_mimeheader($msg, 'UTF-8', 'B', "\r\n", strlen('Subject: '));
}

print_r( $_POST );

$mail = new PHPMailer;
$mail->CharSet = 'utf-8';
ini_set('default_charset', 'UTF-8');
// $encoded_subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n", strlen('Subject: '));

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mail.iis.sinica.edu.tw';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'joanne3634';                 // SMTP username
$mail->Password = 'Lulu0525';                           // SMTP password
$mail->SMTPSecure = 'tls';                     // Enable encryption, 'ssl' also accepted
$mail->IsHTML(true);

$mail->addReplyTo($_POST['contact_email'], $_POST['contact_name']);

$mail->From = 'joanne3634@iis.sinica.edu.tw';
$mail->FromName = 'Appledaily Contact Us';

$mail->addAddress('prj.appledaily@gmail.com', encoding('主要信箱'));     // Add a recipient
$mail->addCC('b00902007@ntu.edu.tw', encoding('CC信箱'));     // Add a recipient
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = encoding('Appledaily Contact Us');
$mail->Body    = $_POST['contact_message'];
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>