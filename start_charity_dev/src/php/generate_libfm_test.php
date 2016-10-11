<?php

require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

if (!isset($_SESSION)) {
	START_SESSION($SESSION_TIME);
}

$__FB_FAV_LIKE_STATUS__ = isset($_GET['fb_fav_like']) ? true : false; 
$__FB_CAT_STATUS__ = isset($_GET['fb_cat']) ? true : false;
$__FB_CATLIST_STATUS__ = isset($_GET['fb_catlist']) ? true : false;
$_W2V_STATUS_ = isset($_GET['w2v']) ? true : false;

$fbid = isset($_GET['fbid'])?$_GET['fbid']:'';  
$uniqId = isset($_GET['uid'])?$_GET['uid']:''; 

$dba = new MYSQL\Accessor();

if( $fbid && $uniqId ){
	$query = $dba->_query('SELECT * FROM `uniq_id` WHERE `uniq_id`= :uid', array(':uid' => $uniqId ));
	if( $result = $query->fetch(PDO::FETCH_ASSOC) ){
		if( $fbid != $result['fb_id'] ){
			echo json_encode(array('status'=>'fail','msg'=>"generate test.libfm fail, fbid: ".$fbid." cannot find." ));
			die();
		}
	}else{
		echo json_encode(array('status'=>'fail','msg'=>"generate test.libfm fail, uid: ".$uniqId." cannot find." ));
		die();
	}
}else{
	echo json_encode(array('status'=>'fail','msg'=>"generate test.libfm fail, fbid or uid missing." ));
	die();
}

$new_dir_logs = '../../db_libfm/'. $uniqId;
if (!file_exists($new_dir_logs)) {
	mkdir($new_dir_logs, 0777, true);
}

$rating_filename = $new_dir_logs.'/test.libfm';
if (file_exists($rating_filename)) {
	unlink($rating_filename);
}

$rating = '';

$pending_filename = '../../db_lists/titles_pending.json';
$libfm_source_path = $DIR_LOGS_ROOT . '/libfm_objects';
$libfm_source_filename = $fbid . '_libfm.json';
$libfm_objects = array();
$libfm_objects = json_decode(file_get_contents($libfm_source_path . '/' . $libfm_source_filename),true);
$item = $libfm_objects['DATA'][$uniqId];

// print_r( $item );
/*** subscribe_frequency  ***/

$serial_id = queryIDbyColumn( 'subscribe_frequency', 'type', 'subscribe_frequency', $dba);			
$subscribe_frequency = '';
if( $libfm_objects['SUBSCRIBING'] > 0 ){
	$subscribe = querySubscribeValue( $libfm_objects['SUBSCRIBING'], $dba );
	$subscribe_frequency = addSpace( $serial_id.':'.$subscribe );
}
// print( $subscribe_frequency );

/*** user 資訊一樣 ***/

// print( $uniqId );
$user_string = '';
foreach ( $item['USER'] as $userIdx => $userValue ) {
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


/*** fb 資訊一樣 ***/

$fb_fav_like = '';
$fb_cat = '';
$fb_cat_list = '';

if ($__FB_FAV_LIKE_STATUS__) {
	foreach ($item['FB']['favorite'] as $fb_id => $fb_name) {
		$serial_id = queryIDbyColumn( substr($fb_id,1), 'name', 'fb_favorite', $dba);
		$fb_fav_like .= addSpace( $serial_id. ':1');
	}
	foreach ($item['FB']['like_id'] as $fb_id => $fb_name) {
		$serial_id = queryIDbyColumn( substr($fb_id,1), 'name', 'fb_like', $dba);
		$fb_fav_like .= addSpace( $serial_id. ':1');
	}
	// print( $fb_fav_like );
}
if ($__FB_CAT_STATUS__) {
	foreach ($item['FB']['like_category'] as $fb_id => $fb_name) {
		$serial_id = queryIDbyColumn( str_replace(' ', '_', substr( $fb_id,1 )), 'name', 'fb_category',$dba);
		$fb_cat .= addSpace( $serial_id. ':1');
	}
	// print( $fb_cat );
}
if ($__FB_CATLIST_STATUS__){
	foreach ($item['FB']['like_category_list'] as $fb_id => $fb_name) {
		$serial_id = queryIDbyColumn( str_replace(' ', '_', substr( $fb_id,1 )), 'name', 'fb_category_list', $dba);
		$fb_cat_list .= addSpace( $serial_id.':1');
	}
	// print( $fb_cat_list );
}



/*** recommend article ***/

$pending_objects = json_decode(file_get_contents( $pending_filename ), true);
foreach ( $pending_objects as $index => $value) {
	// print_r( $value );
	$aid = $value['aid'];
	$score = addSpace('0');
	$word2vec = '';					
	if( $_W2V_STATUS_ ){
		$query_w2v = $dba->_query('SELECT `w2v` FROM `article` WHERE `aid`= :aid', array( ':aid' => $aid ));
		$w2v_data = $query_w2v->fetch(PDO::FETCH_ASSOC);
		$w2v = json_decode( $w2v_data['w2v'],true );
		foreach( $w2v as $w2v_i => $w2v_v ){
			$serial_id = queryIDbyColumn( $w2v_i, 'name', 'word2vec', $dba);
			$word2vec .= addSpace( $serial_id.':'.$w2v_v );
		}
	}
	// print( $word2vec );
	$sub_rating = '';
	$sub_rating .= $score;
	$sub_rating .= addSpace( queryIDbyColumn( $aid, 'name', 'aid', $dba).':1' );
	$sub_rating .= addSpace( queryIDbyColumn( $uniqId, 'name', 'uid', $dba).':1' );
	$sub_rating .= $user_string.$subscribe_frequency.$word2vec;
	if ($__FB_FAV_LIKE_STATUS__) {
		$sub_rating .= $fb_fav_like;
	}
	if ($__FB_CAT_STATUS__) {
		$sub_rating .= $fb_cat;
	}
	if ($__FB_CATLIST_STATUS__){
		$sub_rating .= $fb_cat_list;
	}
	// print( $sub_rating );
	$rating .= addChangeLine( $sub_rating );
}


file_put_contents( $rating_filename, $rating );
// echo json_encode(array('status'=>'success','msg'=>'generate ( fbid: '.$fbid. ',uid: '.$uniqId.' ) test.libfm success','data'=>$rating));
echo json_encode(array('status'=>'success','msg'=>'generate ( fbid: '.$fbid. ',uid: '.$uniqId.' ) test.libfm success'));
	// print( $rating );
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