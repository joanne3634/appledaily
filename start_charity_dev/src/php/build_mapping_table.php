<?php
session_start();
/* ============================

clear 清空資料表
init 基本資料表 不會隨使用者
update 隨使用者更新
all 先清空再全部更新一遍

============================ */

require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

$__CLEAR__ = isset($_GET['clear']) ? true : false;
$__INIT__ = isset($_GET['init']) ? true : false;
$__UPDATE__ = isset($_GET['update']) ? true : false;
$__ALL__ = isset($_GET['all']) ? true : false;

$dba = new MYSQL\Accessor('localhost', 'appledaily', 'joanne3634', '369369');

if ($__CLEAR__ || $__ALL__) {
	echo "clear\n";
	$dba->_execute('TRUNCATE TABLE `article`');
	$dba->_execute('TRUNCATE TABLE `fb_category`');
	$dba->_execute('TRUNCATE TABLE `fb_category_list`');
	$dba->_execute('TRUNCATE TABLE `fb_favorite`');
	$dba->_execute('TRUNCATE TABLE `fb_gender`');
	$dba->_execute('TRUNCATE TABLE `fb_id`');
	$dba->_execute('TRUNCATE TABLE `fb_like`');
	$dba->_execute('TRUNCATE TABLE `subscribe`');
	$dba->_execute('TRUNCATE TABLE `uniq_id`');
	$dba->_execute('TRUNCATE TABLE `user`');
}

if ($__INIT__ || $__ALL__) {
	echo "init\n";
	createSubscribeTable($dba);
	createFbGenderTable($dba);
	createUserTable($dba, $QUESTIONAIRE_DATASET);
	createArticleTable($dba);
}
if ($__UPDATE__ || $__ALL__) {
	echo "update\n";
	$fb_id_array = findMember($createMember = true, $DIR_LOGS_ROOT . '/libfm_objects', $dba);
	// print_r($fb_id_array);
	foreach ($fb_id_array as $fb_id) {
		createFbRelateTable($fav = true, $like = true, $cat = true, $catlist = true, $DIR_LOGS_ROOT . '/facebook_objects', $fb_id, $dba);
	}
}

function createFbRelateTable($CREATE_FB_FAVORITE_STATUS = false, $CREATE_FB_LIKE_STATUS = false, $CREATE_FB_CAT_STATUS = false, $CREATE_FB_CATLIST_STATUS = false, $fb_dir_logs, $fb_id, $dba) {
	if ($CREATE_FB_LIKE_STATUS || $CREATE_FB_CAT_STATUS || $CREATE_FB_CATLIST_STATUS) {
		$fb_likes_filename = $fb_id . '_likes.json';
		$facebook_likes_objects = json_decode(file_get_contents($fb_dir_logs . '/' . $fb_likes_filename), true);
		foreach ($facebook_likes_objects as $key => $value) {

			/*==============  create fb_like table ==============*/
			if ($CREATE_FB_LIKE_STATUS) {
				$dba->_execute(
					'INSERT INTO fb_like VALUE (0,:like_id,:like_name)',
					array(
						':like_id' => $value['id'],
						':like_name' => str_replace(' ', '_', $value['name']),
					)
				);
			}

			/*==============  create fb_category table ==============*/
			if ($CREATE_FB_CAT_STATUS) {
				$dba->_execute(
					'INSERT INTO fb_category VALUE (0,:c_name)',
					array(
						':c_name' => str_replace(' ', '_', $value['category']),
					)
				);
			}

			/*==============  create fb_category_list table ==============*/
			if ($CREATE_FB_CATLIST_STATUS) {
				if (isset($value['category_list'])) {
					foreach ($value['category_list'] as $key_cl => $val_cl) {
						$dba->_execute(
							'INSERT INTO fb_category_list VALUE (0,:cl_name,:cl_id,:c_name)',
							array(
								':cl_name' => str_replace(' ', '_', $val_cl['name']),
								':cl_id' => $val_cl['id'],
								':c_name' => str_replace(' ', '_', $value['category']),
							)
						);
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
					$dba->_execute(
						'INSERT INTO fb_favorite VALUE (0,:fav_id,:fav_name,:fav_type)',
						array(
							':fav_id' => $value['id'],
							':fav_name' => str_replace(' ', '_', $value['name']),
							':fav_type' => $column_name,
						)
					);
				} else {
					break;
				}
			}
		}
	}
}

function createArticleTable($dba) {
	$article_filename = '../../db_lists/titles_1590.json';
	$article_objects = json_decode(file_get_contents($article_filename), true);

	foreach ($article_objects as $index => $value) {
		$dba->_execute(
			'INSERT INTO article VALUE (0,:aid,:article,:cover,:title,:url)',
			array(
				':aid' => $value['aid'],
				':article' => $value['article'],
				':cover' => $value['cover'],
				':title' => str_replace(' ', '_', $value['title']),
				':url' => $value['url'],
			)
		);
	}
}

function createSubscribeTable($dba) {
	$dba->_execute("INSERT INTO `subscribe` (`id`, `subscribe_value`, `subscribe_name`) VALUES (1, 1, '每天一次'),(2, 2, '每週一次'),(3, 3, '每週二次'),(4, 4, '每週三次'),(5, 5, '每週四次'),(6, 6, '每週五次'),(7, 7, '每週六次'),(8, 8, '每月一次'),(9, 9, '每月二次'),(10, 10, '每月三次'),(11, 11, '每月四次'),(12, 12, '每年一次'),(13, 13, '每年二次'),(14, 14, '每年三次'),(15, 15, '每年四次'),(16, 16, '每年五次'),(17, 17, '每年六次'),(18, 18, '每年七次'),(19, 19, '每年八次'),(20, 20, '每年九次'),(21, 21, '每年十次'),(22, 22, '每年十一次'),(23, 23, '每年十二次');");
}

function createFbGenderTable($dba) {
	$dba->_execute("INSERT INTO `fb_gender` (`id`, `gender_id`, `gender_name`) VALUES (1, 0, '男'),(2, 1, '女'),(3, 2, '其他');");
}

function createUserTable($dba, $QUESTIONAIRE_DATASET) {

	foreach ($QUESTIONAIRE_DATASET as $key => $value) {
		foreach ($value as $index => $question_item) {
			$dba->_execute(
				'INSERT INTO user VALUE (0,:user_key,:user_definition)',
				array(
					':user_key' => $key . '-' . $index,
					':user_definition' => $question_item,
				)
			);
		}
	}
	$dba->_execute(
		'INSERT INTO user VALUE (0,:user_key,:user_definition)',
		array(
			':user_key' => "charityWilling",
			':user_definition' => "分享公益募款文章給朋友",
		)
	);
}

function findMember($CREATE_MEMBER_STATUS = false, $libfm_filename, $dba) {
	$fb_id_array = array();
	if ($handle = opendir($libfm_filename)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				// echo "$entry\n";
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
	$dba->_execute(
		'INSERT INTO fb_id VALUE (0,:fbId,:email,:subscribe)',
		array(
			':fbId' => $fb_id,
			':email' => $email,
			':subscribe' => $subscribe,
		)
	);
	foreach ($data as $uniqId => $item) {
		$dba->_execute(
			'INSERT INTO uniq_id VALUE (0,:uniqId,:fbId)',
			array(
				':uniqId' => $uniqId,
				':fbId' => $fb_id,
			)
		);
	}
}
?>