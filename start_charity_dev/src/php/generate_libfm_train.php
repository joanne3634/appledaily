<?php
/** 
 *  $_post['uniq_id'] 若有設定獨立存放 train 進 db_libfm/:uniq_id 資料夾內
 *  default 為最新共用版本 @ db_libfm/
 */
require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

if (!isset($_SESSION)) {
	START_SESSION($SESSION_TIME);
}

$__FB_FAV_LIKE_STATUS__ = isset($_GET['fb_fav_like']) ? true : false; 
$__FB_CAT_STATUS__ = isset($_GET['fb_cat']) ? true : false;
$__FB_CATLIST_STATUS__ = isset($_GET['fb_catlist']) ? true : false;

$UNIQ_ID = (isset($_GET['UNIQ_ID']) || isset($_POST['UNIQ_ID'])) ? ( isset($_GET['UNIQ_ID']) ? $_GET['UNIQ_ID'] : $_POST['UNIQ_ID'] ) : '';

// print( $UNIQ_ID );
$dba = new MYSQL\Accessor('localhost', 'appledaily', 'joanne3634', '369369');

$new_dir_logs = '../../db_libfm/'. $UNIQ_ID;
if (!file_exists($new_dir_logs)) {
	mkdir($new_dir_logs, 0777, true);
}

$rating_filename = $new_dir_logs.'/train.libfm';
if (file_exists($rating_filename)) {
	unlink($rating_filename);
}

$rating = '';
$train_uid_list = json_decode(file_get_contents( $LIBFM_TRAIN_UID_PATH ), true);
// print_r(  $train_uid_list ); 

$libfm_filename = $DIR_LOGS_ROOT .'/libfm_objects';
if ($handle = opendir($libfm_filename)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != ".." && substr( $entry, -10 ) == 'libfm.json' ) {
			// print( $entry );
			$libfm_objects = json_decode(file_get_contents($libfm_filename . '/' . $entry), true);
			
			// 每個 fb_id 的 subscribe_frequency 一樣
			$serial_id = queryIDbyColumn( 'subscribe_frequency', 'type', 'subscribe_frequency', $dba);
			
			$subscribe_frequency = '';
			if( $libfm_objects['SUBSCRIBING'] > 0 ){
				$subscribe = querySubscribeValue( $libfm_objects['SUBSCRIBING'], $dba );
				$subscribe_frequency = addSpace( $serial_id.':'.$subscribe );
			}
			
			// print( $subscribe_frequency );
   			
			foreach ( $libfm_objects['DATA'] as $uniqId => $item ) {	

				if( in_array( $uniqId, $train_uid_list ) == 1 ){
					// 每包 data 的 user 資訊一樣
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
					
					// 每包 data 的 fb 資訊一樣 
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
							$serial_id = queryIDbyColumn( str_replace(' ', '_', substr( $fb_id,1 )) , 'name', 'fb_category',$dba);
							$fb_cat .= addSpace( $serial_id. ':1');
						}
						// print( $fb_cat );
					}
					if ($__FB_CATLIST_STATUS__){
						foreach ($item['FB']['like_category_list'] as $fb_id => $fb_name) {
							$serial_id = queryIDbyColumn( str_replace(' ', '_', substr( $fb_id,1 )), 'name', 'fb_category_list', $dba);
							$fb_cat_list .= addSpace( $serial_id.':1');
						}
					}

					foreach ($item['ROUND'] as $aid => $article) {
						$aid = substr( $aid, 1 );
						// score timestamp w2v 不一樣
						$sub_rating = '';
						$score = '';
						$time_stamp = '';

						if( isset( $article['time']) ){ // FIX: 新版本的規則 for round[{'score','time'}]
							$time_stamp = addSpace( queryIDbyColumn( 'time_stamp', 'type', 'time_stamp', $dba).':'.$article['time'] );
							$score = addSpace( $article['score'] );
						}else{ // 舊版本 只有score 
							$score = addSpace( $article );
						}
						
						$word2vec = '';					
						$query_w2v = $dba->_query('SELECT `w2v` FROM `article` WHERE `aid`= :aid', array( ':aid' => $aid ));
						$w2v_data = $query_w2v->fetch(PDO::FETCH_ASSOC);
						$w2v = json_decode( $w2v_data['w2v'],true );
						foreach( $w2v as $w2v_i => $w2v_v ){
							$serial_id = queryIDbyColumn( $w2v_i, 'name', 'word2vec', $dba);
							$word2vec .= addSpace( $serial_id.':'.$w2v_v );
						}
						// print( $word2vec );
						$sub_rating .= $score.$user_string.$subscribe_frequency.$time_stamp;
						// print( $sub_rating."\n" );
						$sub_rating .= addSpace( queryIDbyColumn( $aid, 'name', 'aid', $dba).':1' );
						$sub_rating .= addSpace( queryIDbyColumn( $uniqId, 'name', 'uid', $dba).':1' );
						$sub_rating .= $word2vec;
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
					// print( $rating );
					file_put_contents( $rating_filename, $rating);
				}
			}
		}
	}
	closedir($handle);
}

print( $rating );

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