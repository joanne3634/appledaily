<?php
session_start();
/* ============================

/{:method}/{:fbId} 
method: {update} 單一 {fb} 資料 

?clear&init&update&all 

clear 清空資料表
init 更新基本資料表包含 article
update 更新所有使用者fb資料
all 先清空再全部更新一遍

============================ */

require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

$__CLEAR__ = isset($_GET['clear']) ? true : false;
$__INIT__ = isset($_GET['init']) ? true : false;
$__UPDATE__ = isset($_GET['update']) ? true : false;
$__ALL__ = isset($_GET['all']) ? true : false;
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$method = array_shift($request);
$key = array_shift($request);

$dba = new MYSQL\Accessor();

if ($__CLEAR__ || $__ALL__) {
	echo json_encode(array('status'=>'success','msg'=>'clear mapping table success!'));
	$dba->_execute('TRUNCATE TABLE `article`');
	$dba->_execute('TRUNCATE TABLE `bounty_worker`');
	$dba->_execute('TRUNCATE TABLE `fb_category`');
	$dba->_execute('TRUNCATE TABLE `fb_category_list`');
	$dba->_execute('TRUNCATE TABLE `fb_favorite`');
	$dba->_execute('TRUNCATE TABLE `fb_gender`');
	$dba->_execute('TRUNCATE TABLE `fb_id`');
	$dba->_execute('TRUNCATE TABLE `fb_like`');
	$dba->_execute('TRUNCATE TABLE `libfm_history`');
	$dba->_execute('TRUNCATE TABLE `libfm_serial`');
	$dba->_execute('TRUNCATE TABLE `prediction`');
	$dba->_execute('TRUNCATE TABLE `subscribe`');
	$dba->_execute('TRUNCATE TABLE `uniq_id`');
	$dba->_execute('TRUNCATE TABLE `user`');
}

if ($__INIT__ || $__ALL__) {
	echo json_encode(array('status'=>'success','msg'=>'init mapping table success!'));
	createSubscribeTable($dba);
	createFbGenderTable($dba);
	createUserTable($dba, $QUESTIONAIRE_DATASET);
	createArticleTable($dba, $W2V_FILEPATH, '../../db_lists/titles_done.json', false );
	createArticleTable($dba, $W2V_FILEPATH, '../../db_lists/titles_pending.json', true );
}
if ($__UPDATE__ || $__ALL__) {
	echo json_encode(array('status'=>'success','msg'=>'update mapping table success!'));
	$fb_id_array = findMember($createMember = true, $DIR_LOGS_ROOT . '/libfm_objects', $dba);
	foreach ($fb_id_array as $fb_id) {
		createFbRelateTable($fav = true, $like = true, $cat = true, $catlist = true, $DIR_LOGS_ROOT . '/facebook_objects', $fb_id, $dba);
	}
}

if( $method == 'update' ){
	$filename = $DIR_LOGS_ROOT . '/libfm_objects/' . $key .'_libfm.json';
	if( $key != '' && file_exists($filename) ){
		$libfm_objects = json_decode(file_get_contents($filename), true);
		createMemberTable($dba, $libfm_objects['DATA'], $libfm_objects['FB_ID'], $libfm_objects['EMAIL'], $libfm_objects['SUBSCRIBING']);
		createFbRelateTable($fav = true, $like = true, $cat = true, $catlist = true, $DIR_LOGS_ROOT . '/facebook_objects', $key, $dba);
		echo json_encode(array('status'=>'success','msg'=>'fbid: '.$key.' update mapping table success!'));
	}else{
		echo json_encode(array('status'=>'fail','msg'=>'fbid libfm not found, cannot update mapping table!'));
	}
	
} 

function createFbRelateTable($CREATE_FB_FAVORITE_STATUS = false, $CREATE_FB_LIKE_STATUS = false, $CREATE_FB_CAT_STATUS = false, $CREATE_FB_CATLIST_STATUS = false, $fb_dir_logs, $fb_id, $dba) {
	if ($CREATE_FB_LIKE_STATUS || $CREATE_FB_CAT_STATUS || $CREATE_FB_CATLIST_STATUS) {
		$fb_likes_filename = $fb_id . '_likes.json';
		$facebook_likes_objects = json_decode(file_get_contents($fb_dir_logs . '/' . $fb_likes_filename), true);
		foreach ($facebook_likes_objects as $key => $value) {

			/*==============  create fb_like table ==============*/
			if ($CREATE_FB_LIKE_STATUS) {
				$query = $dba->_query(
							'SELECT * FROM `fb_like` WHERE `like_id`= :like_id and `like_name`= :like_name ',
							array(
								':like_id' => $value['id'],
								':like_name' => str_replace(' ', '_', $value['name'])
							)
						);

				if( $query->rowCount() == 0 ){
					$dba->_execute(
						'INSERT INTO fb_like VALUE (0,:like_id,:like_name)',
						array(
							':like_id' => $value['id'],
							':like_name' => str_replace(' ', '_', $value['name'])
						)
					);
				}
			}

			/*==============  create fb_category table ==============*/
			if ($CREATE_FB_CAT_STATUS) {
				$query = $dba->_query(
							'SELECT * FROM `fb_category` WHERE `c_name`= :c_name',
							array(
								':c_name' => str_replace(' ', '_', $value['category'])
							)
						);
				if( $query->rowCount() == 0 ){
					$dba->_execute(
						'INSERT INTO fb_category VALUE (0,:c_name)',
						array(
							':c_name' => str_replace(' ', '_', $value['category'])
						)
					);
				}
			}

			/*==============  create fb_category_list table ==============*/
			if ($CREATE_FB_CATLIST_STATUS) {
				if (isset($value['category_list'])) {
					foreach ($value['category_list'] as $key_cl => $val_cl) {
						$query = $dba->_query('SELECT * FROM `fb_category_list` WHERE `cl_name`= :cl_name and `cl_id`= :cl_id and `c_name`= :c_name',
								array(
									':cl_name' => str_replace(' ', '_', $val_cl['name']),
									':cl_id' => $val_cl['id'],
									':c_name' => str_replace(' ', '_', $value['category'])
								)
							);
						if( $query->rowCount() == 0 ){
							$dba->_execute(
								'INSERT INTO fb_category_list VALUE (0,:cl_name,:cl_id,:c_name)',
								array(
									':cl_name' => str_replace(' ', '_', $val_cl['name']),
									':cl_id' => $val_cl['id'],
									':c_name' => str_replace(' ', '_', $value['category'])
								)
							);
						}
					}
				}
			}
		}
	}

	/*==============  create fb_favorite table ==============*/
	if ($CREATE_FB_FAVORITE_STATUS) {
		$fb_me_filename = $fb_id . '_me.json';
		$facebook_me_objects = json_decode(file_get_contents($fb_dir_logs . '/' . $fb_me_filename), true);
		foreach ($facebook_me_objects as $column_name => $fb_item) {
			foreach ($fb_item as $index => $value) {
				if (isset($value['id']) && isset($value['name'])) {
					$query = $dba->_query('SELECT * FROM `fb_favorite` WHERE `fav_id`= :fav_id and `fav_name`= :fav_name and `fav_type`= :fav_type',
							array(
								':fav_id' => $value['id'],
								':fav_name' => str_replace(' ', '_', $value['name']),
								':fav_type' => $column_name
							)
						);
					if( $query->rowCount() == 0 ){
						$dba->_execute(
							'INSERT INTO fb_favorite VALUE (0,:fav_id,:fav_name,:fav_type)',
							array(
								':fav_id' => $value['id'],
								':fav_name' => str_replace(' ', '_', $value['name']),
								':fav_type' => $column_name,
							)
						);
					}
				} else {
					break;
				}
			}
		}
	}
}


function createArticleTable($dba, $W2V_FILEPATH, $article_filename, $pending = true) {
	$article_objects = json_decode(file_get_contents($article_filename), true);
	$w2v_objects = json_decode(file_get_contents($W2V_FILEPATH), true);
	foreach ($article_objects as $index => $value) {
		$query = $dba->_query('SELECT * FROM `article` WHERE `aid`= :aid', array( ':aid' => $value['aid'] ));
		if( $query->rowCount() == 0 ){
			$dba->_execute(
				'INSERT INTO article VALUE (0,:aid,:article,:title,:url,:count,:pending,:w2v)',
				array(
					':aid' => $value['aid'],
					':article' => $value['article'],
					':title' => str_replace(' ', '_', $value['title']),
					':url' => $value['url'],
					':count' => -1,
					':pending' => $pending,
					':w2v' => ( isset($w2v_objects[ $value['aid'] ][0]) ? json_encode( $w2v_objects[ $value['aid'] ][0] ): null )
	 			)
			);
		}else{
			$aid = $value['aid'];
			$dba->_execute("UPDATE `article` SET `pending`= ". ($pending?1:0) ." WHERE `aid`='{$aid}'");
		}
	}
}

function createSubscribeTable($dba) {
	$dba->_execute('TRUNCATE TABLE `subscribe`');
	$dba->_execute("INSERT INTO `subscribe` (`id`, `subscribe_value`, `subscribe_name`) VALUES (1, 365, '每天一次'),(2, 52, '每週一次'),(3, 104, '每週二次'),(4, 12, '每月一次'),(5, 24, '每月二次'),(6, 1, '每年一次'),(7, 2, '每年二次'),(8, 3, '每年三次'),(9, 4, '每年四次');");
}

function createFbGenderTable($dba) {
	$dba->_execute('TRUNCATE TABLE `fb_gender`');
	$dba->_execute("INSERT INTO `fb_gender` (`id`, `gender_id`, `gender_name`) VALUES (1, 0, '男'),(2, 1, '女'),(3, 2, '其他');");
}

function createUserTable($dba, $QUESTIONAIRE_DATASET) {
	$dba->_execute('TRUNCATE TABLE `user`');
	foreach ($QUESTIONAIRE_DATASET as $key => $value) {
		foreach ($value as $index => $question_item) {
			$dba->_execute(
				'INSERT INTO user VALUE (0,:user_key,:user_definition)',
				array(
					':user_key' => $key . '-' . $index,
					':user_definition' => $question_item
				)
			);
		}
	}
	$dba->_execute(
		'INSERT INTO user VALUE (0,:user_key,:user_definition)',
		array(
			':user_key' => "charityWilling",
			':user_definition' => "分享公益募款文章給朋友"
		)
	);
}

function findMember($CREATE_MEMBER_STATUS = false, $libfm_filename, $dba) {
	$fb_id_array = array();
	if ($handle = opendir($libfm_filename)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && substr( $entry, -10 ) == 'libfm.json' ) {
				$libfm_objects = json_decode(file_get_contents($libfm_filename . '/' . $entry), true);
				$fb_id = $libfm_objects['FB_ID'];
				$email = $libfm_objects['EMAIL'];
				$subscribe = $libfm_objects['SUBSCRIBING'];

				array_push($fb_id_array, $fb_id);

				/*=====  create fb_id, uniq_id table  =====*/
				if ($CREATE_MEMBER_STATUS) {
					createMemberTable($dba, $libfm_objects['DATA'], $fb_id, $email, $subscribe);
				}
			}
		}
		closedir($handle);
	}
	return $fb_id_array;
}

function createMemberTable($dba, $data, $fb_id, $email, $subscribe) {
	$query = $dba->_query('SELECT * FROM `fb_id` WHERE `fb_id`= :fbId', array(':fbId' => $fb_id ));
	if( $query->rowCount() == 0 ){
		$dba->_execute(
			'INSERT INTO fb_id VALUE (0,:fbId,:email,:subscribe, 0,0,0,0,0,0)',
			array(
				':fbId' => $fb_id,
				':email' => $email,
				':subscribe' => $subscribe
			)
		);
	}

	foreach ($data as $uniqId => $item) {
		$query = $dba->_query('SELECT * FROM `uniq_id` WHERE `uniq_id`= :uniqId', array(':uniqId' => $uniqId ));
		if( $query->rowCount() == 0 ){
			$dba->_execute(
				'INSERT INTO uniq_id VALUE (0,:uniqId,:fbId)',
				array(
					':uniqId' => $uniqId,
					':fbId' => $fb_id
				)
			);
		}
	}
}
?>