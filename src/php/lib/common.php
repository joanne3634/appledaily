<?php
	/*==========  Variables  ==========*/
	$DIR_LOGS_ROOT = '../../www-data';
	$DIR_LOGS = '../../www-data/log_errors';
	$DIR_SUBMITTED = '../../www-data/data_submitted';
	$LOGFILE_PHP_ERR = $DIR_LOGS . '/php-error.log';
	$LOGFILE_EXCEP = 'php_exception.log.csv';
	$SESSION_TIME = 1800;

	/*==========  Redirect the error report  ==========*/
	ini_set("log_errors", 3);
	ini_set("error_log", $LOGFILE_PHP_ERR);

	/*==========  Functions  ==========*/
  function GET_USER_IP() {
    if( isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    	&& $_SERVER['HTTP_X_FORWARDED_FOR'] != NULL) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if( isset($_SERVER['REMOTE_ADDR'])
    	&& $_SERVER['REMOTE_ADDR'] != NULL) {
      $ip = $_SERVER['REMOTE_ADDR'];
    } else {
    	$ip = gethostname();
    }
    return $ip;
  }

	function GET_USER_AGENT() {
		$agent = '';
		if( isset($_SERVER['HTTP_USER_AGENT'])
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
		$body['time']    = GET_CURRENT_TIME();
		$body['uid'] = $uid;
		$body['ip']      = GET_USER_IP();
		$body['browser'] = GET_USER_AGENT();
		$body['message'] = $msg;
		$body['php_exception'] = 'na';

		$log_file = new Log($GLOBALS['DIR_LOGS'] . '/' . $GLOBALS['LOGFILE_EXCEP'], $head);

		try {
		    $log_file -> write_csv($body);
		} catch(Exception $e)  {
			$body['php_exception'] = $e->getMessage();
			$log_file -> write_csv($body);
		}
	}

	function DATA_SUBMITTED_RECORDING($filename, $head, $body) {
		if (!file_exists($GLOBALS['DIR_SUBMITTED'])) {
			mkdir($GLOBALS['DIR_SUBMITTED'], 0777, true);
			chmod($GLOBALS['DIR_SUBMITTED'], 0777);
		}

	    $log_file = new Log($GLOBALS['DIR_SUBMITTED'] . '/' . $filename, $head);

	    try {
		    $log_file -> write_csv($body);
		    return 'success';
	    } catch(Exception $e)  {
	    	return $e->getMessage();
	    }
	}
?>