<?php
	require_once 'lib/common.php';
  	require_once 'lib/log.php';
	require_once 'lib/facebook-sdk/autoload.php';

	use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	use Facebook\GraphUser;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookSDKException;
	use Facebook\FacebookAuthorizationException;
	
	if ( !isset($_SESSION) ) {
		START_SESSION($SESSION_TIME);
	} else {
		END_SESSION();
		START_SESSION($SESSION_TIME);
	}

	if ( isset($_POST['FACEBOOK_UID']) ) {
		echo json_encode(array('response' => 'success!'));
	} else {
		echo json_encode(array('response' => 'fail!'));
		exit;
	}

	/*==========  facebook object saving  ==========*/
	try {
		FacebookSession::setDefaultApplication('342086465999475', 'c9f51180113e45451968718912f68135');
		$session = new FacebookSession($_POST['FACEBOOK_TOKEN']);
	} catch(FacebookSDKException $e) {
		$msg_error = "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
		EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
		exit;
	} catch(FacebookAuthorizationException $e) {
		$msg_error = "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
		EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
		exit;
	} catch(Exception $e) {
		$msg_error = "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
		EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
		exit;
	}

	try {
		// Exchange the short-lived token for a long-lived token.
		$longLivedAccessToken = $session->getAccessToken()->extend();
	} catch(FacebookSDKException $e) {
		$msg_error = "Exception occured, code: " . $e->getCode() . " with message: Error extending short-lived access token: " . $e->getMessage();
		EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
		// exit;
	} catch(Exception $e) {
		$msg_error = "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
		EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
		// exit;
	}

	/*==========  the access token logging  ==========*/
	$new_dir_logs = $DIR_LOGS_ROOT . '/facebook_access_token';
	if(!file_exists($new_dir_logs)) mkdir($new_dir_logs, 0777, true);
	$head = array('time', 'uid', 'access_token', 'expires_at');

	$ret = array();
	$ret['time']          = date('Ymd-His');
	$ret['uid']           = $_POST['FACEBOOK_UID'];

	if (isset($longLivedAccessToken)) {
		$ret['access_token'] = $longLivedAccessToken->__toString();
		$ret['expires_at'] = $longLivedAccessToken->getExpiresAt()->format('Ymd-His');
	} else {
		$ret['access_token'] = $_POST['FACEBOOK_TOKEN'];
		$ret['expires_at'] = date('Ymd-His', strtotime("+2 hours"));
	}
	$filename = $ret['uid'] . '.csv';

	if (file_exists($new_dir_logs . '/' . $filename)) {
	  if (filesize($new_dir_logs . '/' . $filename) > 1024) {
	  	unlink ($new_dir_logs . '/' . $filename);
	  }
	}

	$log_file = new Log($new_dir_logs .'/'. $filename, $head);
	$log_file -> write_csv($ret);

	//	access the information of facebook
	try {
  		$json_options = JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT;

	  	/*==========  profile saving  ==========*/
		$user_profile = (new FacebookRequest(
	      $session, 'GET', '/me'
	    ))->execute()->getGraphObject();

		if (is_object($user_profile)) {
		  	$js = json_encode($user_profile->asArray(), $json_options);
		} else {
			$js = json_encode (json_decode ("{}"));
		}

		$new_dir_logs = $DIR_LOGS_ROOT . '/facebook_objects';
		if(!file_exists($new_dir_logs)) mkdir($new_dir_logs, 0777, true);
		$filename = $_POST['FACEBOOK_UID'] . '_me.json';

		if (file_exists($filename)) unlink($filename);
		file_put_contents($new_dir_logs . '/' . $filename, $js);


		/*==========  likes saving  ==========*/
		$user_likes = (new FacebookRequest(
			$session, 'GET', '/me/likes?limit=100'
		))->execute()->getGraphObject();

		if (is_object($user_likes) && is_object($user_likes->getProperty('data'))) {
			try {
				$data = $user_likes->getProperty('data')->asArray();
				$data_merged = $data;
				while ( count($data) >= 100) {
					$after =  $user_likes->getProperty('paging')->getProperty('cursors')->getProperty('after');
					$next_page = "/me/likes?after=$after&limit=100";
				  	$user_likes = (new FacebookRequest(
				    	$session, 'GET', $next_page
				  	))->execute()->getGraphObject();

					if (is_object($user_likes->getProperty('data'))) {
				  		$data = $user_likes->getProperty('data')->asArray();
				  		$data_merged = array_merge($data_merged, $data);
					} else {
						break;
					}
				}
		  		$js = json_encode($data_merged, $json_options);
		  	} catch (Exception $e) {
		  		$js = json_encode (json_decode ("{}"));
	  		    $msg_error = "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
	  		    EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
		  	}
	  	} else {
	  		$js = json_encode (json_decode ("{}"));
	  	}

		$new_dir_logs = $DIR_LOGS_ROOT . '/facebook_objects';
		if(!file_exists($new_dir_logs)) mkdir($new_dir_logs, 0777, true);
		$filename = $_POST['FACEBOOK_UID'] . '_likes.json';

	  	if (file_exists($filename)) unlink($filename);
		file_put_contents($new_dir_logs . '/' . $filename, $js);


  		/*==========  friends saving  ==========*/
 	  	$user_friends = (new FacebookRequest(
      		$session, 'GET', '/me/friends'
		))->execute()->getGraphObject();

 	  	if (is_object($user_friends)) {
 	  	  $ufriends = $user_friends->asArray();
 	  	  if (empty($ufriends)) {
 	  		$js = json_encode (json_decode ("{}"));
 	  	  } else {
		  	$js = json_encode($ufriends, $json_options);
 	  	  }
 	  	} else {
 	  		$js = json_encode (json_decode ("{}"));
 	  	}

	  	$new_dir_logs = $DIR_LOGS_ROOT . '/facebook_objects';
	  	if(!file_exists($new_dir_logs)) mkdir($new_dir_logs, 0777, true);
	  	$filename = $_POST['FACEBOOK_UID'] . '_friends.json';

	  	if (file_exists($filename)) unlink($filename);
	  	file_put_contents($new_dir_logs . '/' . $filename, $js);
  	} catch(FacebookRequestException $e) {
    	$msg_error = "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
    	EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
	} catch(Exception $e) {
		$msg_error = "Exception occured, code: " . $e->getCode() . " with message: " . $e->getMessage();
		EXCEPTION_RECORDING($msg_error, $_POST['UNIQ_ID']);
	}
?>