<?php
session_start();

require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

$__CLEAR__ = isset($_GET['clear']) ? true : false;
$__FB_FAVORITE_STATUS__ = isset($_GET['fb_fav']) ? true : false; 
$__FB_LIKE_STATUS__ = isset($_GET['fb_like']) ? true : false;
$__FB_CAT_STATUS__ = isset($_GET['fb_cat']) ? true : false;
$__FB_CATLIST_STATUS__ = isset($_GET['fb_catlist']) ? true : false;

$dba = new MYSQL\Accessor('localhost', 'appledaily', 'joanne3634', '369369');

if( $__CLEAR__ ){ $dba->_execute('TRUNCATE TABLE `libfm_serial`'); }

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

$fb_id_array = findMember( $dba, $DIR_LOGS_ROOT . '/libfm_objects',$__FB_FAVORITE_STATUS__, $__FB_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__ ); 
foreach ($fb_id_array as $fb_id) {
	UpdateLibfmTalbe( $dba, "fb_id", $fb_id );
}

function createUserMapping( $dba, $QUESTIONAIRE_DATASET ) {
	foreach ($QUESTIONAIRE_DATASET as $key => $value) {
		foreach ($value as $index => $question_item) {
			UpdateLibfmTalbe( $dba, "user_profile", $question_item, $key.'-'.$index );
		}
	}
}


function createFB( $FB_FAVORITE_STATUS = false, $FB_LIKE_STATUS = false, $FB_CAT_STATUS = false, $FB_CATLIST_STATUS = false, $data, $dba){

	if ($FB_FAVORITE_STATUS) {
		foreach ($data['favorite'] as $fb_id => $fb_name) {
			UpdateLibfmTalbe( $dba, "fb_favorite", substr($fb_id,1), "fb_favorite");
		}
	}
	if( $FB_LIKE_STATUS ){
		foreach ($data['like_id'] as $fb_id => $fb_name) {
			UpdateLibfmTalbe( $dba, "fb_like", substr($fb_id,1), "fb_like");
		}
	}
	if ($FB_CAT_STATUS) {
		foreach ($data['like_category'] as $fb_id => $fb_name) {
			UpdateLibfmTalbe( $dba, "fb_category", str_replace(' ', '_', substr( $fb_id,1 ) ), "fb_category");
		}
	}
	if ($FB_CATLIST_STATUS){
		foreach ($data['like_category_list'] as $fb_id => $fb_name) {
			UpdateLibfmTalbe( $dba, "fb_category_list", str_replace(' ', '_', substr( $fb_id,1 )), "fb_category_list");
		}
	}
}

function findMember( $dba, $libfm_filename,$__FB_FAVORITE_STATUS__, $__FB_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__ ) {
	$fb_id_array = array();
	if ($handle = opendir($libfm_filename)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && substr( $entry, -10 ) == 'libfm.json') {
				$libfm_objects = json_decode(file_get_contents($libfm_filename . '/' . $entry), true);
				$fb_id = $libfm_objects['FB_ID'];
				array_push($fb_id_array, $fb_id);

				foreach ($libfm_objects['DATA'] as $uniqId => $item) {
					UpdateLibfmTalbe( $dba, "uid", $uniqId );
					createFB( $__FB_FAVORITE_STATUS__, $__FB_LIKE_STATUS__, $__FB_CAT_STATUS__, $__FB_CATLIST_STATUS__, $item['FB'], $dba );
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