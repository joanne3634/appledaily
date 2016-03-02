<?php
	require_once 'lib/common.php';
	require_once 'lib/log.php';

	if (!isset($_SESSION)) START_SESSION($SESSION_TIME);

	if (isset($_POST['uniqId'])) {
		/*==========  pages' start time  ==========*/
		$head = array('startLanding', 'startSubscribing', 'startQuestionaire', 'startSurvey', 'startRecommendation', 'startThanks');
		$body = array();

		$decoded = json_decode($_POST['timeRecording'], true);

		$body['startLanding'] = $decoded['startLanding'];
		$body['startSubscribing'] = $decoded['startSubscribing'];
		$body['startQuestionaire'] = $decoded['startQuestionaire'];
		$body['startSurvey'] = $decoded['startSurvey'];
		$body['startRecommendation'] = $decoded['startRecommendation'];
		$body['startThanks'] = $decoded['startThanks'];

		$expr_index = '_' . $_POST['hashKey'];

		if (strcmp($_POST['ip'], 'na') == 0) {
			$my_ip = GET_USER_IP();
		} else {
			$my_ip = $_POST['ip'];
		}

		$filename = $_POST['uniqId'] . '_' . $my_ip . $expr_index . '_pages.csv';
		$ret = 'na';
		$ret = DATA_SUBMITTED_RECORDING($filename, $head, $body);
		if ($ret != 'success') {
			$msg = $ret;
			EXCEPTION_RECORDING($msg, $_POST['uniqId']);
			echo json_encode(array('status'=>'fail', 'message'=>$msg));
		} else {
			if ($decoded['timingType'] == 0) {
				$report = 'na';
			} else {
				$report = GET_RANDSTR(10);
			}

			$head = array('time', 'uid', 'ip', 'browser', 'file', 'expr_id', 'expr_subid', 'facebook_id', 'number_cases');
			$body = array();
			$body['time'] = GET_CURRENT_TIME();
			$body['uid'] = $_POST['uniqId'];
			$body['ip'] = $my_ip;
			$body['browser'] = GET_USER_AGENT();
			$body['file'] = $_POST['uniqId'] . '_' . $my_ip . '_*';
			$body['expr_id'] = $_POST['expId'];
			$body['expr_subid'] = $_POST['expSubId'];
			$body['facebook_id'] = $_POST['fbId'];
			$body['number_cases'] = $_POST['numCases'];

		  	$filename = $_POST['uniqId'] . '_' . $my_ip . $expr_index . '_info.csv';
		  	if (file_exists($GLOBALS['DIR_SUBMITTED'] . '/' . $filename)) unlink($GLOBALS['DIR_SUBMITTED'] . '/' . $filename);

			$ret = DATA_SUBMITTED_RECORDING($filename, $head, $body);
			if ($ret != 'success') {
				$msg = $ret;
				EXCEPTION_RECORDING($msg, $_POST['uniqId']);
				echo json_encode(array('status'=>'fail', 'message'=>$msg));
			} else {
				echo json_encode(array('status'=>'success', 'report'=>$report));
			}
		}
	} else {
		$msg = 'the variable uniqId not exist';
    	EXCEPTION_RECORDING($msg, $_POST['uniqId']);
		echo json_encode(array('status'=>'fail', 'message'=>$msg));
	}
?>