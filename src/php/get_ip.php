<?php
	require_once 'lib/common.php';
	require_once 'lib/log.php';

	$ip = 'na';

	// Gets the IP address
	$ip = GET_USER_IP ();

	echo json_encode(array('ip' => $ip));
?>