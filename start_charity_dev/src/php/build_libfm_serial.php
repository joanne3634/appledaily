<?php
session_start();
/* ============================

/{:method}/{:key} 
{update} 單一 {uniqId} 資料 
{all} 全部建立 有任何更新
{clear} 清空 libfm_serial 

?fb_fav_like&fb_cat&fb_catlist 
需要建立哪部分的 fb 相關資料 

============================ */
require_once 'lib/mysql.php';
require_once 'lib/common.php';

$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$method = array_shift($request);
$key = array_shift($request);
// print( $method );
// print( $key );

$_FB_STATUS = array();
$_FB_STATUS['fb_fav_like'] = isset($_GET['fb_fav_like']) ? true : false; 
$_FB_STATUS['fb_cat'] = isset($_GET['fb_cat']) ? true : false;
$_FB_STATUS['fb_catlist'] = isset($_GET['fb_catlist']) ? true : false;
// print_r( $_FB_STATUS );

$dba = new MYSQL\Accessor();

if( $method ){
	switch ( $method ) {
		case 'update':
			$query = $dba->_query('SELECT * FROM `uniq_id` WHERE `uniq_id`= :uniqId', array(':uniqId' => $key ));
 			if( $result = $query->fetch(PDO::FETCH_ASSOC) ){
 				$fbId = $result['fb_id'];
 				UpdateLibfmTalbe( $dba, "uid", $key );
 				$libfm_objects = json_decode(file_get_contents( $DIR_LOGS_ROOT . '/libfm_objects/' . $fbId .'_libfm.json'), true);
				createFB( $_FB_STATUS['fb_fav_like'], $_FB_STATUS['fb_cat'], $_FB_STATUS['fb_catlist'], $libfm_objects['FB'], $dba );
				echo json_encode(array('status'=>'success','msg'=>'uid: '.$key.' update libfm serial success.'));
 			}else{
 				echo json_encode(array('status'=>'fail','msg'=>'uid: '.$key.' not found.'));
 			}
			break;
		case 'all': 
			_all( $dba,$QUESTIONAIRE_DATASET, $W2V_FILEPATH, $DIR_LOGS_ROOT, $_FB_STATUS ); 
			echo json_encode(array('status'=>'success','msg'=>'update all libfm serial success.'));
			break;
		case 'clear': 
			$dba->_execute('TRUNCATE TABLE `libfm_serial`'); 
			echo json_encode(array('status'=>'success','msg'=>'clear libfm serial success.'));
			break;
		default:
			_all( $dba,$QUESTIONAIRE_DATASET, $W2V_FILEPATH, $DIR_LOGS_ROOT, $_FB_STATUS ); 
			echo json_encode(array('status'=>'success','msg'=>'update all libfm serial success.')); 
			break;
	}
} 

function _all( $dba,$QUESTIONAIRE_DATASET, $W2V_FILEPATH, $DIR_LOGS_ROOT,$_FB_STATUS = array() ){
	createUserMapping( $dba, $QUESTIONAIRE_DATASET ); // 0 - 87 
	UpdateLibfmTalbe( $dba, "share_intention", "分享公益募款文章給朋友", "charityWilling"); // 88
	UpdateLibfmTalbe( $dba, "subscribe_frequency", "訂閱頻率", "subsribe"); // 89
	UpdateLibfmTalbe( $dba, "time_stamp", "最後更改文章分數時間"); //90

	// aid
	$article = $dba->_query('SELECT `aid` FROM `article` WHERE 1');
	$article_aid_list = $article->fetchAll(PDO::FETCH_ASSOC);
	foreach( $article_aid_list as $aid ){
		// print_r($aid);
		UpdateLibfmTalbe( $dba, "aid", $aid['aid'], "article"); //aid 91
	}

	$w2v_objects = json_decode(file_get_contents($W2V_FILEPATH), true);
	foreach ( $w2v_objects as $i => $item) {
		foreach ( $item[0] as $key => $value) {
			UpdateLibfmTalbe( $dba, "word2vec", $key );
		}
		break;
	}

	$fb_id_array = findMember( $dba, $DIR_LOGS_ROOT . '/libfm_objects', $_FB_STATUS['fb_fav_like'], $_FB_STATUS['fb_cat'], $_FB_STATUS['fb_catlist'] ); 
	foreach ($fb_id_array as $fb_id) UpdateLibfmTalbe( $dba, "fb_id", $fb_id );
}


function createUserMapping( $dba, $QUESTIONAIRE_DATASET ) {
	foreach ($QUESTIONAIRE_DATASET as $key => $value) {
		foreach ($value as $index => $question_item) {
			UpdateLibfmTalbe( $dba, "user_profile", $question_item, $key.'-'.$index );
		}
	}
}


function createFB( $FB_FAVORITE_LIKE_STATUS = false, $FB_CAT_STATUS = false, $FB_CATLIST_STATUS = false, $data, $dba){
	if ($FB_FAVORITE_LIKE_STATUS) {
		foreach ($data['favorite'] as $fb_id => $fb_name) {
			UpdateLibfmTalbe( $dba, "fb_favorite", substr($fb_id,1), "fb_favorite");
		}
		foreach ($data['like_id'] as $fb_id => $fb_name) {
			UpdateLibfmTalbe( $dba, "fb_like", substr($fb_id,1), "fb_like");
		}
	}
	if ($FB_CAT_STATUS) {
		foreach ($data['like_category'] as $fb_id => $fb_name) {
			// print( $fb_id );
			// print( $fb_name );
			// print( "\n");
			UpdateLibfmTalbe( $dba, "fb_category", str_replace(' ', '_', substr( $fb_id,1 ) ), "fb_category");
		}
	}
	if ($FB_CATLIST_STATUS){
		foreach ($data['like_category_list'] as $fb_id => $fb_name) {
			UpdateLibfmTalbe( $dba, "fb_category_list", str_replace(' ', '_', substr( $fb_id,1 )), "fb_category_list");
		}
	}
}

function findMember( $dba, $libfm_filename,$__FB_FAVORITE_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__ ) {
	$fb_id_array = array();
	if ($handle = opendir($libfm_filename)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && substr( $entry, -10 ) == 'libfm.json') {
				$libfm_objects = json_decode(file_get_contents($libfm_filename . '/' . $entry), true);
				$fb_id = $libfm_objects['FB_ID'];
				array_push($fb_id_array, $fb_id);

				foreach ($libfm_objects['DATA'] as $uniqId => $item) {
					UpdateLibfmTalbe( $dba, "uid", $uniqId );
					createFB( $__FB_FAVORITE_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__, $item['FB'], $dba );
				}
			}
		}
		closedir($handle);
	}
	return $fb_id_array;
}

function UpdateLibfmTalbe( $dba, $type, $name, $category="" ){
	$libfm = $dba->_query('SELECT * FROM `libfm_serial` WHERE `category`= :category and `name`= :name and `type`= :type',
			array(
				':category' => $category,
				':name' => $name,
				':type' => $type
			));
	if( $libfm->rowCount() == 0 ){
		$dba->_execute(
			'INSERT INTO libfm_serial VALUE (0,:type,:name,:category)',
			array(
				':category' => $category,
				':name' => $name,
				':type' => $type
			)
		);
	}
}
?>