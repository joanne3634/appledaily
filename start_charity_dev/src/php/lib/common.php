<?php
/*==========  Variables  ==========*/
$DIR_LOGS_ROOT = '../../www-data';
$DIR_LOGS = '../../www-data/log_errors';
$DIR_SUBMITTED = '../../www-data/data_submitted';
$LOGFILE_PHP_ERR = $DIR_LOGS . '/php-error.log';
$LOGFILE_EXCEP = 'php_exception.log.csv';
$SESSION_TIME = 1800;
$W2V_FILEPATH = '../../db_lists/a2v-1.json';
$LIBFM_TRAIN_UID_PATH = '../../db_lists/uid.json';

// 記得要連 js/ui_texts.js 一起改
$QUESTIONAIRE_DATASET = array(
	"gender" => array('男', '女', '其他'),
	"age" => array('18歲或以下', '19-24歲','25-34歲','35-44歲','45-54歲','55-64歲','65歲或以上'),
	"education"=>array('國小','國中','高中','高職','專科','大學','碩士','博士'),
	"marriage"=>array('未婚','已婚無子女','已婚有子女','離婚／失婚無子女','離婚／失婚有子女','其他'),
	"religion"=>array('無','佛教／道教','基督教','天主教','伊斯蘭教','一貫道','其他'),
	"career"=>array('在職(包含soho,接案)','待業','學生','家管','退休'),
	"careerUsed"=>array('經營／人資類','行銷／企劃／專案管理類','餐飲／旅遊／美容美髮類','操作／技術／維修類','營建／製圖類','文字／傳媒工作類','學術／教育／輔導類','生產製造／品管／環衛類','財會／金融專業類','行政／總務／法務類','客服／門市／業務／貿易類','資訊軟體系統類','資材／物流／運輸類','傳播藝術／設計類','醫療／保健服務類','研發相關類','軍警消／保全類','農林漁牧相關類','其他職類'),
	"income"=>array('20萬以下','20-30萬','30-50萬','50-80萬','80-120萬','120-150萬','150萬以上'),
	"charityHistory"=>array('平均每個月數次','平均每個月一次','平均每二個月一次','平均每半年一次','平均每年一次','沒有印象'),
	"charityTendency"=>array('捐款至對您有意義或對您在乎的人有幫助的非營利組織，例如您所就讀的母校。','捐款至普遍大型的非營利組織，由於該組織的活動為多數人所認同，且您也容易進行捐款，例如慈濟基金會。','捐款至富理想性的非營利組織，由於您認為他們做的事是最為重要的，例如國際特赦組織。','捐款至與您的宗教信仰符合的非營利組織，例如教會與寺廟。','捐款至當地的非營利組織，通常您的捐款對他們而言是一筆不小的幫助，這會讓您確切感受到您的付出。','捐款至您所熟識的人建立的非營利組織，因個人因素而讓您想幫助他(她)所建立的組織。','其他'),
	"charityActivity"=>array('捐贈發票','捐贈物資','捐血／捐骨髓','購買公益彩卷','環保／社區發展','身心障礙者服務','老人服務','兒童服務','動物保護','公益健走／路跑','課業輔導','參與其他類型的志工活動'),
);
$FACEBOOK_GENDER=array('male','female');
/*==========  Redirect the error report  ==========*/
ini_set("log_errors", 3);
ini_set("error_log", $LOGFILE_PHP_ERR);

/*==========  Functions  ==========*/
function GET_USER_IP() {
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
		&& $_SERVER['HTTP_X_FORWARDED_FOR'] != NULL) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if (isset($_SERVER['REMOTE_ADDR'])
		&& $_SERVER['REMOTE_ADDR'] != NULL) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip = gethostname();
	}
	return $ip;
}

function GET_USER_AGENT() {
	$agent = '';
	if (isset($_SERVER['HTTP_USER_AGENT'])
		&& $_SERVER['HTTP_USER_AGENT'] != NULL) {
		$agent = $_SERVER['HTTP_USER_AGENT'];
	} else {
		$agent = 'console';
	}
	return $agent;
}

function GET_RANDSTR($length) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$chooselength = strlen($chars);
	$string = '';
	for ($i = 0; $i < ((int) $length); $i++) {
		$string .= $chars[mt_rand() % $chooselength];
	}
	return $string;
}

function START_SESSION($expire = 0) {
	if ($expire == 0) {
		$expire = ini_get('session.gc_maxlifetime');
	} else {
		ini_set('session.gc_maxlifetime', $expire);
		ini_set('session.gc_probability', 0);
	}

	if (empty($_COOKIE['PHPSESSID'])) {
		session_set_cookie_params($expire);
		session_start();
	} else {
		session_start();
		setcookie('PHPSESSID', session_id(), time() + $expire);
	}
}

function END_SESSION() {
	session_unset();
}

function GET_CURRENT_TIME() {
	return date('Ymd-His');
}

function EXCEPTION_RECORDING($msg, $uid) {
	if (!file_exists($GLOBALS['DIR_LOGS'])) {
		mkdir($GLOBALS['DIR_LOGS'], 0777, true);
		chmod($GLOBALS['DIR_LOGS'], 0777);
	}

	$head = array('time', 'uid', 'ip', 'browser', 'message', 'php_exception');
	$body = array();
	$body['time'] = GET_CURRENT_TIME();
	$body['uid'] = $uid;
	$body['ip'] = GET_USER_IP();
	$body['browser'] = GET_USER_AGENT();
	$body['message'] = $msg;
	$body['php_exception'] = 'na';

	$log_file = new Log($GLOBALS['DIR_LOGS'] . '/' . $GLOBALS['LOGFILE_EXCEP'], $head);

	try {
		$log_file->write_csv($body);
	} catch (Exception $e) {
		$body['php_exception'] = $e->getMessage();
		$log_file->write_csv($body);
	}
}

function DATA_SUBMITTED_RECORDING($filename, $head, $body) {
	if (!file_exists($GLOBALS['DIR_SUBMITTED'])) {
		mkdir($GLOBALS['DIR_SUBMITTED'], 0777, true);
		chmod($GLOBALS['DIR_SUBMITTED'], 0777);
	}

	$log_file = new Log($GLOBALS['DIR_SUBMITTED'] . '/' . $filename, $head);

	try {
		$log_file->write_csv($body);
		return 'success';
	} catch (Exception $e) {
		return $e->getMessage();
	}
}
?>