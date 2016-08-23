<?php
require '../src/php/lib/PHPMailer/PHPMailerAutoload.php';

mb_internal_encoding('UTF-8');

function encoding($msg) {
	return mb_encode_mimeheader($msg, 'UTF-8', 'B', "\r\n", strlen('Subject: '));
}
function category( $category_value ){
	switch ( $category_value ) {
		case '1':
			return '問題回報';
			break;
		case '2':
			return '合作洽談';
			break;
		default:
			return '其他';
	}
} 

if( !$_POST['contact_email'] ){ 
	echo json_encode( array('status'=>'error','msg'=> 'The email cannot be empty.') );
	return false; 
}else if( !$_POST['contact_category'] ){ 
	echo json_encode( array('status'=>'error','msg'=> 'The category cannot be empty.') );
	return false; 
}else if( !$_POST['contact_message'] ){ 
	echo json_encode( array('status'=>'error','msg'=> 'The message cannot be empty.') );
	return false; 
}

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
$mail->FromName = 'Appledaily Support';

$mail->addAddress('prj.appledaily@gmail.com', encoding('主要信箱'));     // Add a recipient
$mail->addBCC('b00902007@ntu.edu.tw', encoding('CC信箱'));     // Add a recipient
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = encoding('訪客訊息');
$mail->Body = '   
<div style="line-height: 20px;">

聯絡姓名: '. (!$_POST['contact_name'] ? "匿名": $_POST['contact_name']) .'<br>
聯絡信箱: '. $_POST['contact_email'] .'<br>
問題分類: '. category($_POST['contact_category']) .'<br>
訊息主旨: '. $_POST['contact_subject'] .'<br>
訊息內容: '. $_POST['contact_message'].'<br>
</div>
';


// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
	echo json_encode( array('status'=>'error','msg'=> $mail->ErrorInfo) );
	return false;
} else {
    echo json_encode( array('status'=>'success','msg'=> 'Message has been sent'));
    return true;
}

?>