<?php
require_once 'lib/mysql.php';
require_once 'lib/common.php';
require_once 'lib/log.php';
$dba = new MYSQL\Accessor();

$new_dir_logs = $DIR_LOGS_ROOT . '/bounty_worker';
if (!file_exists($new_dir_logs)) {
	mkdir($new_dir_logs, 0777, true);
}

$head = array('time', 'bountyId', 'uid', 'fbid');

if( !$_POST['bounty_name'] || !$_POST['bounty_uid'] || !$_POST['bounty_fbid']){
	echo json_encode(array('status'=>'fail','msg'=>'missing parameter'));
	exit();
}
$ret = array();
$ret['time'] = date('Ymd-His');
$ret['bountyId'] = $_POST['bounty_name'];
$ret['uid'] = $_POST['bounty_uid'];
$ret['fbid'] = $_POST['bounty_fbid'];

$msg = '';

$bounty = $dba->_query('SELECT * FROM `bounty_worker` WHERE `bounty_id`= :bounty_id and `uid`= :uid',
		array(
			':bounty_id' => $ret['bountyId'],
			':uid' => $ret['uid']
		));
if( $bounty->rowCount() == 0 ){
	$dba->_execute(
		'INSERT INTO bounty_worker VALUE (0,:bounty_id,:uid)',
		array(
			':bounty_id' => $ret['bountyId'],
			':uid' => $ret['uid']
		)
	);
	$filename = 'bounty_worker.csv';
	$log_file = new Log($new_dir_logs . '/' . $filename, $head);
	$log_file->write_csv($ret);
	$msg = '此組合第一次回報, 回報成功!';
}else{
	$msg = '此組合已回報過!';
}
echo json_encode(array('status'=>'success','msg'=>$msg));

?>