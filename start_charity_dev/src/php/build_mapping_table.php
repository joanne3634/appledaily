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
	createUserTable($dba);
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

function createUserTable($dba) {
	$QUESTIONAIRE_DATASET = array(
		"gender" => array('男', '女'),
		"age" => array('18歲或以下', '19-24歲', '25-34歲', '35-44歲', '45-54歲', '55-64歲', '65歲或以上'),
		"education" => array('國小', '國中', '高中', '高職', '專科', '大學', '碩士', '博士'),
		"marriage" => array('未婚', '已婚無子女', '已婚有子女', '離婚／失婚無子女', '離婚／失婚有子女', '其他'),
		"religion" => array('無', '佛教／道教', '基督教', '天主教', '伊斯蘭教', '一貫道', '其他'),
		"career" => array('在職', '待業', '學生', '家管', '退休'),
		"careerUsed" => array('經營／人資類', '行銷／企劃／專案管理類', '餐飲／旅遊／美容美髮類', '操作／技術／維修類', '營建／製圖類', '文字／傳媒工作類', '學術／教育／輔導類', '生產製造／品管／環衛類', '財會／金融專業類', '行政／總務／法務類', '客服／門市／業務／貿易類', '資訊軟體系統類', '資材／物流／運輸類', '傳播藝術／設計類', '醫療／保健服務類', '研發相關類', '軍警消／保全類', '農林漁牧相關類', '其他職類'),
		"income" => array('20萬以下', '21-30萬', '31-50萬', '51-80萬', '81-120萬', '121-150萬', '151萬以上'),
		"charityHistory" => array('平均每個月數次', '平均每個月一次', '平均每二個月一次', '平均每半年一次', '平均每年一次', '沒有印象'),
		"charityTendency" => array('捐款至對您有意義或對您在乎的人有幫助或您曾受過幫助的非營利組織，例如您所就讀的母校。', '捐款至普遍大型的非營利組織，由於該組織的活動為多數人所認同，且您也容易進行捐款，例如聯合勸募。', '捐款至富理想性的非營利組織，由於您認為他們做的事是最為重要的，例如國際特赦組織。', '捐款至與您的宗教信仰符合的非營利組織，例如教會與寺廟。', '捐款至當地的非營利組織，通常您的捐款對他們而言是一筆不小的幫助，這會讓您確切感受到您的付出。', '捐款至您所熟識的人建立的非營利組織，因個人因素而讓您想幫助他(她)所建立的組織。'),
		"charityActivity" => array('捐贈發票', '捐贈物資', '捐血／捐骨髓', '購買公益彩卷', '環保／社區發展', '身心障礙者服務', '老人服務', '兒童服務', '動物保護', '公益健走／路跑', '課業輔導', '參與其他類型的志工活動'),
	);

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