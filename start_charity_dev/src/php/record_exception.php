<?php
	require_once 'lib/common.php';
	require_once 'lib/log.php';

	$msg = 'FROM CLIENT: ' . $_POST['exceptionMsg'];
	if (isset($_POST['uniqId'])) {
		EXCEPTION_RECORDING($msg, $_POST['uniqId']);
		echo json_encode(array('status'=>'success'));
	} else {
		$msg = 'POST["uniqId"] not exist';
    	EXCEPTION_RECORDING($msg, 'na');
		echo json_encode(array('status'=>'fail', 'message'=>$msg));
	}
?>