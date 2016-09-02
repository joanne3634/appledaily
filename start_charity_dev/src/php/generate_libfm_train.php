<?php
/** 

/{:method}/?fbid={:fbid}&uid={:uid}&aid={:aid}&score={:score}&timestamp={:timestamp}
/?fbid=10208714434367191&uid=1472548289055CfB&aid=A3903&score=64.21&timestamp=1472548332781

method: 
uid_single_train require: fbid, uid, aid, score, timestamp  @ db_libfm/{:uid}/train.libfm
uid_set_train require: fbid, uid @ db_libfm/{:uid}/train.libfm   

&fb_fav_like&fb_cat&fb_catlist  
 
default 為更新共用版本 @ db_libfm/train.libfm

 */
require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

if (!isset($_SESSION)) {
	START_SESSION($SESSION_TIME);
}
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$method = array_shift($request);
// print( $method . "\n" );

$__FB_FAV_LIKE_STATUS__ = isset($_GET['fb_fav_like']) ? true : false; 
$__FB_CAT_STATUS__ = isset($_GET['fb_cat']) ? true : false;
$__FB_CATLIST_STATUS__ = isset($_GET['fb_catlist']) ? true : false;

$uid =  isset($_GET['uid'])? $_GET['uid'] : '';

$dba = new MYSQL\Accessor('localhost', 'appledaily', 'joanne3634', '369369');


$default_dir_path = '../../db_libfm/';
if (!file_exists($default_dir_path)) mkdir($default_dir_path, 0777, true);
$default_train_filename = $default_dir_path.'/train.libfm';

$new_dir_path = '../../db_libfm/'. $uid;
if (!file_exists($new_dir_path)) mkdir($new_dir_path, 0777, true);
$rating_filename = $new_dir_path.'/train.libfm';

$rating = '';
switch ( $method ) {
	case 'uid_single_train':
		
		$fbid = $_GET['fbid'];
		// print $fbid. "\n";
		$uid = $_GET['uid']; 
		// print $uid. "\n";
		$aid = $_GET['aid']; 
		// print $aid. "\n";
		$score = $_GET['score'];
		// print $score. "\n";
		$timestamp = $_GET['timestamp'];
		// print $timestamp. "\n";

		if( $fbid && $uid && $aid && $score && $timestamp ){
			// print('required match!');
			$libfm_objects = json_decode(file_get_contents( $DIR_LOGS_ROOT . '/libfm_objects/' . $fbid .'_libfm.json'), true);
			$subscribe_frequency = str_subscribe($dba, $libfm_objects['SUBSCRIBING']);
			if (file_exists($rating_filename)){
				$rating = file_get_contents($rating_filename);
			}else{
				$rating = file_get_contents($default_train_filename);
			}
			$rating .= addChangeLine( user_train( $dba, $libfm_objects['DATA'][$uid], $subscribe_frequency, $fbid, $uid, $__FB_FAV_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__,'uid_single_train', $aid, addSpace($score), addSpace( queryIDbyColumn( 'time_stamp', 'type', 'time_stamp', $dba).':'.$timestamp ))); 
			file_put_contents( $rating_filename, $rating);
			echo json_encode(array('status'=>'success','msg'=>'method: uid_single_train, '.$fbid. " ".$uid. " ".$aid. " ".$score. " ".$timestamp));
		}else{
			// print('required missing');
			echo json_encode(array('status'=>'fail','msg'=>'method: uid_single_train, required missing.'));
		}
		
		break;
	case 'uid_set_train':  
		$fbid = $_GET['fbid'];
		// print $fbid. "\n";
		$uid = $_GET['uid']; 
		// print $uid. "\n";

		if( $fbid && $uid ){
			// print('required match!');
			$query = $dba->_query('SELECT * FROM `uniq_id` WHERE `uniq_id`= :uid', array(':uid' => $uid ));
 			if( $result = $query->fetch(PDO::FETCH_ASSOC) ){
 				if( $fbid == $result['fb_id'] ){
 					$libfm_objects = json_decode(file_get_contents( $DIR_LOGS_ROOT . '/libfm_objects/' . $fbid .'_libfm.json'), true);
					$subscribe_frequency = str_subscribe($dba, $libfm_objects['SUBSCRIBING']);
					
					$rating = file_get_contents($default_train_filename);
					$rating .= addChangeLine( user_train( $dba, $libfm_objects['DATA'][$uid], $subscribe_frequency, $fbid, $uid, $__FB_FAV_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__)); 
					if (file_exists($rating_filename)) unlink($rating_filename);
					file_put_contents( $rating_filename, $rating);
					echo json_encode(array('status'=>'success','msg'=>'method: uid_set_train, '.$fbid. " ".$uid));

 				}else{
 					echo json_encode(array('status'=>'fail','msg'=>'method: uid_set_train, fbid cannot find.'));
 				}
 			}else{
				echo json_encode(array('status'=>'fail','msg'=>'method: uid_set_train, uid cannot find.'));
			}
			
		}else{
			// print('required missing');
			echo json_encode(array('status'=>'fail','msg'=>'method: uid_set_train, required missing.'));
		}
	break;
	default: 
		$libfm_filename = $DIR_LOGS_ROOT .'/libfm_objects';
		if ($handle = opendir($libfm_filename)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && substr( $entry, -10 ) == 'libfm.json' ) {
					$libfm_objects = json_decode(file_get_contents($libfm_filename . '/' . $entry), true);
					$subscribe_frequency = str_subscribe($dba, $libfm_objects['SUBSCRIBING']);
					foreach ( $libfm_objects['DATA'] as $uniqId => $item ) {	
						$rating .= user_train( $dba, $item, $subscribe_frequency, $libfm_objects['FB_ID'], $uniqId, $__FB_FAV_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__) ;					
					}
				}
			}
			closedir($handle);
		}
		if (file_exists($default_train_filename)) unlink($default_train_filename);
		file_put_contents( $default_train_filename, $rating);
		echo json_encode(array('status'=>'success','msg'=>'method: default'));
	break;
}

// $train_uid_list = json_decode(file_get_contents( $LIBFM_TRAIN_UID_PATH ), true);
// print_r(  $train_uid_list ); 


function user_train( $dba, $item, $subscribe_frequency, $fbid, $uid, $__FB_FAV_LIKE_STATUS__=false, $__FB_CAT_STATUS__=false, $__FB_CATLIST_STATUS__ =false, $method ='', $aid='', $score='', $time_stamp='')
{
	// print_r( $item);
	$user_string = str_user( $dba, $item['USER'] );
	// print $user_string ."\n";
	// list( $fb_fav_like, $fb_cat, $fb_cat_list ) 
	list( $fb_fav_like, $fb_cat, $fb_cat_list ) = str_fb( $dba, $item['FB'], $__FB_FAV_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__);
	$rating = '';

	if( $method == 'uid_single_train'){
		$rating = str_sub_rating($dba, $score,$user_string,$subscribe_frequency,$time_stamp,$aid,$uid, str_w2v( $dba, $aid ) ,$fb_fav_like,$fb_cat,$fb_cat_list );
	}else{
		foreach ( $item['ROUND'] as $aid => $article) {
			$aid = substr( $aid, 1 );
			list($score, $time_stamp) = str_score_time( $dba , $article );	
			$sub_rating = str_sub_rating($dba, $score,$user_string,$subscribe_frequency,$time_stamp,$aid,$uid, str_w2v( $dba, $aid ) ,$fb_fav_like,$fb_cat,$fb_cat_list );
			$rating .= addChangeLine( $sub_rating );
		}
	}
	return $rating;
}
function str_subscribe($dba, $subscribe){
	// print( $subscribe );
	$serial_id = queryIDbyColumn( 'subscribe_frequency', 'type', 'subscribe_frequency', $dba);
	$subscribe_frequency = '';
	if( $subscribe > 0 ){
		$subscribe_value = querySubscribeValue( $subscribe, $dba );
		if( $subscribe_value > 0 ){
			$subscribe_frequency = addSpace( $serial_id.':'.$subscribe );
		}
	}
	return $subscribe_frequency;
}
function str_sub_rating($dba, $score,$user_string,$subscribe_frequency,$time_stamp,$aid,$uid,$word2vec,$fb_fav_like,$fb_cat,$fb_cat_list){
	$sub_rating = $score.$user_string.$subscribe_frequency.$time_stamp;
	$sub_rating .= addSpace( queryIDbyColumn( $aid, 'name', 'aid', $dba).':1' );
	$sub_rating .= addSpace( queryIDbyColumn( $uid, 'name', 'uid', $dba).':1' );
	$sub_rating .= $word2vec;
	$sub_rating .= $fb_fav_like.$fb_cat.$fb_cat_list;
	return $sub_rating ;
}
function str_fb( $dba, $fb_data, $__FB_FAV_LIKE_STATUS__=false, $__FB_CAT_STATUS__=false, $__FB_CATLIST_STATUS__=false){
	$fb_fav_like = '';
	$fb_cat = '';
	$fb_cat_list = '';

	if ($__FB_FAV_LIKE_STATUS__) {
		foreach ($fb_data['favorite'] as $fb_id => $fb_name) {
			$serial_id = queryIDbyColumn( substr($fb_id,1), 'name', 'fb_favorite', $dba);
			$fb_fav_like .= addSpace( $serial_id. ':1');
		}
		foreach ($fb_data['like_id'] as $fb_id => $fb_name) {
			$serial_id = queryIDbyColumn( substr($fb_id,1), 'name', 'fb_like', $dba);
			$fb_fav_like .= addSpace( $serial_id. ':1');
		}
		// print( $fb_fav_like );
	}
	if ($__FB_CAT_STATUS__) {
		foreach ($fb_data['like_category'] as $fb_id => $fb_name) {
			$serial_id = queryIDbyColumn( str_replace(' ', '_', substr( $fb_id,1 )) , 'name', 'fb_category',$dba);
			$fb_cat .= addSpace( $serial_id. ':1');
		}
		// print( $fb_cat );
	}
	if ($__FB_CATLIST_STATUS__){
		foreach ($fb_data['like_category_list'] as $fb_id => $fb_name) {
			$serial_id = queryIDbyColumn( str_replace(' ', '_', substr( $fb_id,1 )), 'name', 'fb_category_list', $dba);
			$fb_cat_list .= addSpace( $serial_id.':1');
		}
	}
	// print_r( array('fb_fav_like'=>$fb_fav_like,'fb_cat'=>$fb_cat,'fb_cat_list'=>$fb_cat_list) );
	return array($fb_fav_like,$fb_cat,$fb_cat_list);
}
function str_user( $dba, $user ){
	$user_string = '';
	foreach ( $user as $userIdx => $userValue ) {
		if ( $userValue != 0 ) {
			if( $userIdx == 'charityWilling' ) {
				$serial_id = queryIDbyColumn( $userIdx, 'category', 'share_intention', $dba);
				$user_string .= addSpace( $serial_id.':'.$userValue );
			}else{
				$serial_id = queryIDbyColumn( $userIdx, 'category', 'user_profile', $dba);
				$user_string .= addSpace( $serial_id.':1' );
			}
		}
	}
	// print( $user_string );	
	return $user_string;
}
function str_score_time( $dba , $article ){
	$time_stamp = '';
	$score = '';

	if( isset( $article['time']) ){ // FIX: 新版本的規則 for round[{'score','time'}]
		$time_stamp = addSpace( queryIDbyColumn( 'time_stamp', 'type', 'time_stamp', $dba).':'.$article['time'] );
		$score = addSpace( $article['score'] );
	}else{ // 舊版本 只有score 
		$score = addSpace( $article );
	}
	return array( $score, $time_stamp );
}

function str_w2v( $dba, $aid ){
	$word2vec = '';					
	$query_w2v = $dba->_query('SELECT `w2v` FROM `article` WHERE `aid`= :aid', array( ':aid' => $aid ));
	$w2v_data = $query_w2v->fetch(PDO::FETCH_ASSOC);
	$w2v = json_decode( $w2v_data['w2v'],true );
	foreach( $w2v as $w2v_i => $w2v_v ){
		$serial_id = queryIDbyColumn( $w2v_i, 'name', 'word2vec', $dba);
		$word2vec .= addSpace( $serial_id.':'.$w2v_v );
	}
	// print( $word2vec );
	return $word2vec;
}
function queryIDbyColumn( $value, $column, $type, $dba) {
	$user_profile = $dba->_query(
		'SELECT `id` FROM `libfm_serial` WHERE `type`= :type and `'. $column .'`= :value ',
		array(
			':type' => $type,
			':value' => $value
		)
	);
	if(!$user_profile)
	{
	  die("Execute query error, because: ". $dba->errorInfo());
	}
	//success case
	else{
	    $user_profile_id = $user_profile->fetch(PDO::FETCH_ASSOC);
		return $user_profile_id['id'];
	}
}

function querySubscribeValue( $subscribe_index, $dba ){
	$subscribe_mapping = $dba->_query('SELECT `subscribe_value` FROM `subscribe` WHERE `id`= '. $subscribe_index );
	$subscribe = $subscribe_mapping->fetch(PDO::FETCH_ASSOC);
	return $subscribe['subscribe_value'];
}

function addSpace($string) {
	return $string . " ";
}
function addChangeLine($string) {
	return $string . "\n";
}

?>