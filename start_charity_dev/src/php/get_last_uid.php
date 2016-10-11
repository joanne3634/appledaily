<?php
	require_once 'lib/mysql.php';

	$dba = new MYSQL\Accessor();
	$fbid = $_GET['fbid']; 

	$query = $dba->_query('SELECT `uniq_id` FROM `uniq_id` WHERE `fb_id`=:fbid ORDER BY `uniq_id`.`id` DESC LIMIT 1', array(':fbid' => $fbid ));
	if( $result = $query->fetch(PDO::FETCH_ASSOC) ){
		echo json_encode(array('status'=>'success','msg'=>$result['uniq_id'] ));
		die();
	}else{
		echo json_encode(array('status'=>'fail','msg'=>'fbid: '.$fbid.' not found.'));
	}
	// echo json_encode(array('ip' => $ip));
?>