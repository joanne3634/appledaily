<?php
session_start();
require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';

$dba = new MYSQL\Accessor('localhost', 'appledaily', 'joanne3634', '369369');
$database = array(
	"user" => array('id', 'user_key', 'user_definition'),
	"uniq_id" => array('id', 'uniq_id', 'fb_id'),
	"subscribe" => array('id', 'subscribe_value', 'subscribe_name'),
	"fb_like" => array('id', 'like_id', 'like_name'),
	"fb_id" => array('id', 'fb_id'),
	"fb_gender" => array('id', 'gender_id', 'gender_name'),
	"fb_favorite" => array('id', 'fav_id', 'fav_name', 'fav_type'),
	"fb_category_list" => array('id', 'cl_name', 'cl_id', 'c_name'),
	"fb_category" => array('id', 'c_name'),
	"article" => array('id', 'aid', 'article', 'title', 'url', 'count','w2v'),
	"libfm_serial" => array('id','type','name')
);

foreach ($database as $table => $column) {

	$filedir = '../../db_mapping/';
	if (!file_exists($filedir)) {
		mkdir($filedir, 0777, true);
		chmod($filedir, 0777);
	}
	$filename = $filedir . $table . '.csv';

	if (file_exists($filename)) {
		unlink($filename);
	}

	$out = '';
	$header = columnAddQuote($column, "'");
	$column_name = columnAddQuote($column, "`");
	$query = $dba->_query("SELECT $header UNION ALL SELECT $column_name FROM $table WHERE 1");
	while ($item = $query->fetch(PDO::FETCH_ASSOC)) {
		$count_item = count($item);
		$count = 0;
		foreach ($item as $value) {
			if ($count < ($count_item - 1)) {
				$out .= $value . ' ';
			} else {
				$out .= $value;
			}
			$count++;
		}
		$out .= "\n";
	}
	file_put_contents($filename, $out);
}

function columnAddQuote($column, $mark) {
	$string = '';
	foreach ($column as $value) {
		$string .= "$mark" . $value . "$mark,";
	}
	return substr($string, 0, -1);
}
?>

