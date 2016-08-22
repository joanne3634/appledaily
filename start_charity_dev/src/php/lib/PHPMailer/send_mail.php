<?php
require 'PHPMailerAutoload.php';

mb_internal_encoding('UTF-8');

function encoding($msg) {
	return mb_encode_mimeheader($msg, 'UTF-8', 'B', "\r\n", strlen('Subject: '));
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

$mail->From = 'joanne3634@iis.sinica.edu.tw';
$mail->FromName = encoding('DSC 2016 系列活動場佈組');
$mail->addAddress('joanne3634@yahoo.com.tw', encoding('Joanne'));     // Add a recipient

$mail->addCC('b00902007@ntu.edu.tw', encoding('Joanne'));     // Add a recipient
$mail->addCC('joanne3634@gmail.com', encoding('Joanne'));     // Add a recipient
// $mail->addBCC('erinerintu@gmail.com', encoding('Erin'));     // Add a recipient
// $mail->addBCC('helen.chang@iis.sinica.edu.tw', encoding('Helen'));     // Add a recipient
// $mail->addBCC('wendy@iis.sinica.edu.tw', encoding('Wendy'));     // Add a recipient
// $mail->addBCC('yipeitu@iis.sinica.edu.tw', encoding('YiPei'));     // Add a recipient
// $mail->addBCC('cmchang@iis.sinica.edu.tw', encoding('Jimmy'));     // Add a recipient
// $mail->addBCC('joanne3634@iis.sinica.edu.tw', encoding('Joanne'));     // Add a recipient

// $mail->addBCC('falsewinds@gmail.com', encoding('kj'));     // Add a recipient
// $mail->addBCC('falsewinds@gmail.com', encoding('子承'));     // Add a recipient
// $mail->addBCC('hsuchihfan@gmail.com', encoding('silver'));     // Add a recipient
// $mail->addBCC('rusi@iis.sinica.edu.tw', encoding('Rusi'));     // Add a recipient
// $mail->addBCC('PoHeng.Chen@gmail.com', encoding('柏亨'));     // Add a recipient

// $mail->addBCC('elexy.fw@gmail.com', encoding('hj'));     // Add a recipient
// $mail->addBCC('chiaqinglin@gmail.com', encoding('家慶'));     // Add a recipient
// $mail->addBCC('sttagomantis@gmail.com', encoding('Kuan'));     // Add a recipient
// $mail->addBCC('cchuang.tw@gmail.com', encoding('政清'));     // Add a recipient

// $mail->addBCC('j19920727@gmail.com', encoding('Rex'));     // Add a recipient
// $mail->addBCC('annika83824@gmail.com', encoding('易芸'));     // Add a recipient




// $mail->addBCC('mcshihs@gmail.com', encoding('Conan'));     // Add a recipient
// $mail->addReplyTo('info@example.com', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = encoding('DSC 系列活動-4/30 場佈');
$mail->Body    = '各位熱心的工作人員、志工，大家好：<br>
					這<span style="color: red;">周五 (4/29)</span> 將要進行 系列活動的場佈<br>
					請閱讀我們的工作人員通知：<br><br>

					※ 集合時間與地點<br>
					集合時間：<span style="color: red;">2016/4/29 (五) 14:00</span><br>
					地點: <span style="color: red;">中央研究院資訊科學研究所 6F 602室</span><br><br>
					※ 工作時間概要<br>
					14:00 -- 14:30     清點物資<br>
					14:30 -- 14:45     搬運物資至人文館<br>
					14:45 -- 16:30     場地布置<br>
					詳細工作細項會於當天公布。<br><br>

					※ 交通資訊
					中研院地圖、會場資訊、交通等資訊請見 http://datasci.tw/event/data_visualization/#traffic。<br>
					如您開車前來，車輛可停人文社會科學館 B2 停車場 (空間可停 100 輛)，或停放於中研院區內停車格。<br><br>

					若有任何疑問，歡迎與我們聯繫。<br><br>

					年會網站: http://datasci.tw/<br>
					年會粉絲頁: https://www.facebook.com/twdsconf<br>
					資料科學愛好者交流區: https://www.facebook.com/groups/datasci.tw/<br><br>

					台灣資料科學愛好者年會 籌備小組<br>
					聯絡信箱：twconf.datascience@gmail.com';
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>