<?php 
header("location: http://search.appledaily.com.tw/charity/guest/step/step1/");
require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

$uid = $_GET['uid'];
$aid = $_GET['aid'];
$lib_his_id = $_GET['lib_his_id'];
$source = $_GET['source']; 

$dba = new MYSQL\Accessor();
$dba->_execute(
		'INSERT INTO donate_log VALUE (0,:uid, :aid, :lib_his_id, :source, NOW())',
		array(
			':uid' => $uid,
			':aid' => $aid,
			':lib_his_id' => $lib_his_id,
			':source' => $source 
		)
	);

?>