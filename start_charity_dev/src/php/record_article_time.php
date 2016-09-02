<?php
	require_once 'lib/common.php';
	require_once 'lib/log.php';

	if (!isset($_SESSION)) START_SESSION($SESSION_TIME);
	// time , caseNum , action , uniqId 
	if (isset($_POST['uniqId'])) {
		/*==========  pages' start time  ==========*/
		$head = array('caseRound', 'caseStart', 'caseEnd', 'caseId');
		$body = array();
		// $decoded = json_decode($_POST['round'], true);
		
		$body['caseRound'] = $_POST['round'];
		$body['caseStart'] = $_POST['start'];
		$body['caseEnd'] = $_POST['end'];
		$body['caseId'] = $_POST['id'];

		$expr_index = '_' . $_POST['hashKey'];

		if (strcmp($_POST['ip'], 'na') == 0) {
			$my_ip = GET_USER_IP();
		} else {
			$my_ip = $_POST['ip'];
		}

		$filename = $_POST['uniqId'] . '_' . $my_ip . $expr_index . '_survey_time.csv';
		$ret = 'na';
		$ret = DATA_SUBMITTED_RECORDING($filename, $head, $body);
		// print( $ret );
		if ($ret != 'success') {
			$msg = $ret;
			EXCEPTION_RECORDING($msg, $_POST['uniqId']);
			echo json_encode(array('status'=>'fail', 'message'=>$msg));
		}else{
			echo json_encode(array('status'=>'success'));
		} 
	} else {
		$msg = 'the variable uniqId not exist';
    	EXCEPTION_RECORDING($msg, $_POST['uniqId']);
		echo json_encode(array('status'=>'fail', 'message'=>$msg));
	}
?>