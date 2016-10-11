<?php 

require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';
require 'lib/PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;

$url = 'http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev';
// $url = 'https://7b3bf4dd.ngrok.io';
// https://23a6f680.ngrok.io/index.php?history_id=1&fb_id=10208714434367191# 
$dba = new MYSQL\Accessor();

$subscribe_table = selectSubscribeTable( $dba ); 
$source = 1; // web:0 , email:1 
$recommend_amout = 10;


$query = $dba->_query('SELECT *, TIMESTAMPDIFF(DAY,`last_send`,NOW()) as days FROM `fb_id`');
while( $result = $query->fetch(PDO::FETCH_ASSOC) ){
	$fb_id = $result['fb_id'];
	// print($fb_id ."\n");

	$freq = $subscribe_table[ $result['subscribe'] ] > 0 ? floor(365/$subscribe_table[ $result['subscribe'] ]): 0;
	// print('freq:'. $freq."\n");
	$days = $result['days'];
	// null 還沒寄過
	// print('days:'.$days."\n");
	$email_body = '';
	$donater_email = $result['email'];

	if( $donater_email != 'na' && ($days == null || $days > $freq) ){
		$response = file_get_contents( $url.'/src/php/recommend_list.php?fbid='.$fb_id.'&train_method=uid_from_last&time_status=0&w2v=0&fb_fav_like=0&fb_cat=0&fb_catlist=0');
		$response = json_decode($response,true);
		// print_r( $response );
		if( $response['status'] != 'success' ){
			$dba->_execute(
				'INSERT INTO subscribe_log VALUE (0,:fb_id,:error_msg)',
				array(
					':fb_id' => $fb_id,
					':error_msg' => json_decode($response,true)['msg']
				)
			);
		}else{
			$aid_list = $response['msg']['aid'];
			$history_id = $response['msg']['history_id'];
			$uid = $response['msg']['uid'];
			$count = 0;
			foreach ($aid_list as $aid) {
				if ( $count > $recommend_amout ){
					break;
				}
				$aid_query = $dba->_query("SELECT * from article where `aid`='".$aid."'");
				$aid_data = $aid_query->fetch(PDO::FETCH_ASSOC);
				$aid_title = str_replace('_', ' ', $aid_data['title']);

				// print( 'aid: '. $aid ."\n" );
				// print( 'history_id: '. $history_id ."\n" );
				// print( 'fb_id: '. $fb_id ."\n" );
				// print( 'aid_title: '. $aid_title ."\n" );
				// print( 'source: '. $source ."\n" );

				$email_body .= '<tr><td style="cursor: pointer;padding: 5px;text-align: left;">'.$aid.'</td>
       			 <td style="cursor: pointer;padding: 5px;text-align: left;"><a href="'.$url.'/index.php?history_id='.$history_id.'&fb_id='.$fb_id.'" target="_blank">'.$aid_title.'</a></td><td><a href="http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/php/donate_redirect.php?uid='.$uid.'&lib_his_id='.$history_id.'&aid='.$aid.'&source='.$source.'" target="_blank"><img border="0" src="http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/img/ico/subscribe_button.png" style="width:80px;height: auto; border-width:0px;border-style:solid"></a></td></tr>';
       			$count ++;
			}
		}
		sendEmail( $mail, 'joanne3634@gmail.com', $email_body );
	}
	
}

function encoding($msg) {
	return mb_encode_mimeheader($msg, 'UTF-8', 'B', "\r\n", strlen('Subject: '));
}
function selectSubscribeTable( $dba ){
	$subscribe_table = array();
	$query = $dba->_query('SELECT * FROM `subscribe`');
	while( $result = $query->fetch(PDO::FETCH_ASSOC) ){
		$subscribe_table[$result['id']]= $result['subscribe_value'];
	}
	// print_r( $subscribe_table );
	return $subscribe_table;
}
function sendEmail( $mail, $donater_email, $email_body ){
	mb_internal_encoding('UTF-8');
	$mail->CharSet = 'utf-8';
	ini_set('default_charset', 'UTF-8');

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'mail.iis.sinica.edu.tw';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'joanne3634';                 // SMTP username
	$mail->Password = 'Lulu0525';                           // SMTP password
	$mail->SMTPSecure = 'tls';                     // Enable encryption, 'ssl' also accepted
	$mail->IsHTML(true);
	// $mail->addReplyTo($_POST['contact_email'], $_POST['contact_name']);
	$mail->From = 'joanne3634@iis.sinica.edu.tw';
	$mail->FromName = '急難家庭曙光再現計畫';
	$mail->addAddress( $donater_email , encoding('捐款人'));     // Add a recipient
	// $mail->addBCC('b00902007@ntu.edu.tw', encoding('CC信箱'));     // Add a recipient
	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Subject = encoding('推薦文章');
	$mail->Body = '<div style="width:100%!important;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;margin-bottom:0;margin-left:0;margin-right:0;margin-top:0;font-family:\'Helvetica\',\'Arial\',sans-serif;"><table bgcolor="#f2f2f2" cellspacing="0" style="background:#f2f2f2;width:100%!important;height:100%!important;width:100%!important;padding-top:0;padding-bottom:0;padding-left:0;padding-right:0;margin-bottom:0;margin-left:0;margin-right:0;margin-top:0" width="100%"><tbody><tr><td align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td align="left" style="padding-top:30px;padding-bottom:0px;padding-left:10px;padding-right:10px" valign="top" width="580"><table bgcolor="#ffffff" cellpadding="0" cellspacing="0" style="background:#fff;margin-bottom:30px;padding:0;border-radius:3px;border-bottom:2px solid #d8d6d1" width="100%"><tbody><tr><td valign="top"><a href="http://go.addthis.com/e/92742/dashboard-gallery/3cnhnh/162020084" target="_blank" data-saferedirecturl="https://www.google.com/url?hl=zh-TW&amp;q=http://go.addthis.com/e/92742/dashboard-gallery/3cnhnh/162020084&amp;source=gmail&amp;ust=1475822720744000&amp;usg=AFQjCNH6ss4wPXXV8yAH8Xk-kh4yNtT0Nw"><img alt="" border="0" src="http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/img/ico/appledaily_email_main.png" style="width:600px; border-width:0px;border-style:solid"></a></td></tr><tr><td valign="top"><div style="padding:30px;color:rgb(135,135,135);font-family:Arial;"><p style="line-height: 25px; font-size:15px;">親愛的捐款人,</p><p style="line-height: 25px; font-size:15px;">此文章列表為您可能較有意願捐款的報導；若您有意幫助，請您回覆對該篇文章的捐款意願，或直接按下「前往捐款」按鈕，透過蘋果日報慈善基金會捐助急難家庭。</p><div style="padding:.5em 1em; border: solid;"><table id="recommend-container" style="width: 100%;font-size:18px;"><tbody>'.$email_body.'</tbody></table></div><div style="color:rgb(135,135,135);line-height:25px;word-break:break-word"><p style="color:rgb(135,135,135);line-height:25px;font-size:15px;">任何問題? &nbsp;<a href="mailto:prj.appledaily@gmail.com" target="_blank">歡迎聯絡我們</a></p><p style="color:rgb(135,135,135);line-height:25px;font-size:15px;"><img align="left" alt="ChristianBrink" hspace="8" src="http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/img/ico/appledaily_logo.png" style="float:left;margin-left:0px" width="46">Cheers,<br> 急難家庭曙光再現計畫</p></div></div></td></tr></tbody></table><table cellpadding="2" width="100%"><tbody><tr><td align="center"><p style="font-family:\'Helvetica\',\'Arial\',sans-serif;color:#9ca1ae;font-size:13px;font-weight:300;text-align:center">© 2016 急難家庭曙光再現計畫</p></td></tr><tr><td align="center"><p style="font-family:\'Helvetica Neue\',\'Helvetica\',\'Arial\',sans-serif;font-size:13px;font-weight:300;color:#a5a5a5">請勿回覆此信件 &nbsp;<a href="http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev" style="color:#777" target="_blank">更新推薦頻率請至官網</a></p></td></tr></tbody></table></td><td>&nbsp;</td></tr></tbody></table></td></tr></tbody></table></div>';


	// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$mail->send()) {
		echo json_encode( array('status'=>'error','msg'=> $mail->ErrorInfo) );
		return false;
	} else {
	    echo json_encode( array('status'=>'success','msg'=> 'Message has been sent'));
	    return true;
	}
}
?>