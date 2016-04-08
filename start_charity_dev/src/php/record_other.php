<?php

require_once 'lib/common.php';
require_once 'lib/log.php';

$new_dir_logs = $DIR_LOGS_ROOT . '/other';
if (!file_exists($new_dir_logs)) {
	mkdir($new_dir_logs, 0777, true);
}

$head = array('time', 'fbid', 'uid', 'name', 'value');

$ret = array();
$ret['time'] = date('Ymd-His');
$ret['name'] = $_POST['name'];
$ret['value'] = $_POST['value'];
$ret['uid'] = $_POST['uid'];
$ret['fbid'] = $_POST['fbid'];

$filename = 'other.csv';

$log_file = new Log($new_dir_logs . '/' . $filename, $head);
$log_file->write_csv($ret);

?>