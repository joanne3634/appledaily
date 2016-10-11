<?php
	require_once 'lib/mysql.php';
	require_once 'lib/common.php';
  	require_once 'lib/log.php';
  	$dba = new MYSQL\Accessor();

	$fbid = $_GET['fbid']; 
	$uid = $_GET['uid'];
	$aid = $_GET['aid']; 
	$score = $_GET['score'];
	$timestamp = $_GET['timestamp'];
	$method = $_GET['train_method'];
	// $method = subscribe_email 
	$fb_fav_like = $_GET['fb_fav_like'] ? '&fb_fav_like=1':'';
	$fb_cat = $_GET['fb_cat'] ? '&fb_cat=1':'';
	$fb_catlist = $_GET['fb_catlist'] ? '&fb_catlist=1':'';
	$fb_str = $fb_fav_like . $fb_cat . $fb_catlist; 

	// print( $fb_str );
	$w2v = $_GET['w2v'] ? '&w2v=1':'';
	$time_status = $_GET['time_status'] ? '&time_status=1':'';

	$url = 'http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev';
	// $url = 'https://7b3bf4dd.ngrok.io';
	
	if( $method == 'subscribe_email' ){
		// history_id=2&fbid=10208714434367191&uid=1475516050673Ypw&train_method=subscribe_email
		$query = $dba->_query("SELECT * from libfm_history where `id`=".$_GET['history_id']);
		$history = $query->fetch(PDO::FETCH_ASSOC);
		// print_r( $history );
		$aid = json_decode($history['uid_lists']);
		$pre = json_decode($history['pre']);

		// print_r( $aid );
		// print_r( $pre );
		$uid = $history['uid'];
		$aid_score = array();
		foreach ($aid as $key => $value) {
			// print($value);
			$aid_score[$value] = -1;
			$query = $dba->_query("SELECT * from prediction where `lib_his_id`=".$_GET['history_id']." and `aid`='".$value."' and `uid`='".$uid."'");
			$prediction_data = $query->fetch(PDO::FETCH_ASSOC);
			if( $prediction_data != null ){
				$score = $prediction_data['y'];
				$aid_score[$value] = $score;
			}
		}
		// print_r( $aid_score );
		echo json_encode(array('status'=>'success','msg'=>array('aid'=>$aid,'pre'=>$pre,'history_id'=>$_GET['history_id'],'uid'=>$uid,'aid_score'=>$aid_score)));
	}else{
		

		if( $method == 'uid_from_last'){
			$uid = checkStatus( $last_uid = file_get_contents($url.'/src/php/get_last_uid.php?fbid='.$fbid) );
		}

		checkStatus( $response = file_get_contents($url.'/src/php/build_mapping_table.php/update/'.$fbid));
		checkStatus( $response = file_get_contents($url.'/src/php/build_libfm_serial.php/update/'.$uid.'?'. $fb_str));

		if( $method == 'uid_single_train'){
			checkStatus( $response = file_get_contents($url.'/src/php/generate_libfm_train.php/uid_single_train?fbid='.$fbid.'&uid='.$uid.'&aid='.$aid.'&score='.$score.'&timestamp='.$timestamp. $fb_str.$w2v.$time_status ));
		}
		if($method == 'uid_set_train' || $method == 'uid_from_last'){
			// checkStatus( $response = file_get_contents($url.'/src/php/generate_libfm_train.php?'.$fb_str.$w2v.$time_status));

			checkStatus( $response = file_get_contents($url.'/src/php/generate_libfm_train.php/uid_set_train?fbid='.$fbid.'&uid='.$uid.$fb_str.$w2v.$time_status ));
		}


		checkStatus( $response = file_get_contents($url.'/src/php/generate_libfm_test.php?'.$w2v.$fb_str.'&fbid='.$fbid.'&uid='.$uid));

		// print( $history_id );


		exec('Rscript /home/helen/recommender/libfm.R "'.$uid.'" 2>&1',$output);
		// print_r( $output );
		$uid_lists = file_get_contents('/home/helen/recommender/data/libfm/out_data/libfm_result_'.$uid.'.json');
		// echo json_encode(array('status'=>'success','msg'=>json_decode($uid_lists)));
		$pre_lists = file_get_contents('/home/helen/recommender/data/libfm/out_data/libfm_yp_'.$uid.'.json');
		
		$train = file_get_contents('../../db_libfm/'. $uid.'/train.libfm');
		$test = file_get_contents('../../db_libfm/'. $uid.'/test.libfm');
		
		$history_id = $dba->_execute(
				'INSERT INTO libfm_history VALUE (0,:uid, :train, :test, :uid_lists, :pre, NOW() )',
				array(
					':uid' => $uid,
					':train' => $train,
					':test' => $test,
					':uid_lists' => $uid_lists,
					':pre' => json_encode(json_decode($pre_lists)[0])
				)
			);

		echo json_encode(array('status'=>'success','msg'=>array('aid'=>json_decode($uid_lists),'pre'=>json_decode($pre_lists)[0],'history_id'=>$history_id,'uid'=>$uid)));
	}

	function checkStatus( $res )
	{
		// print_r( $res );
		if( json_decode($res,true)['status'] != 'success'){
			// print_r( $res );
			// return json_decode($res,true);
			exit();
		}
		return json_decode($res,true)['msg'];
	}
?>