<?
  require_once 'lib/mysql.php';
  require_once 'lib/common.php';
  require_once 'lib/log.php';

  $dba = new MYSQL\Accessor();
  $uid = $_POST['uid'];
  $lib_his_id = $_POST['lib_his_id'];
  $feedback = json_decode($_POST['feedback'],true);
  $prList = $_POST['prList'];
  
  if( $uid != null && $lib_his_id != null && $feedback !=null && $prList != null ){
    
    foreach ($feedback as $key => $value) {
      $aid = $value['aid'];
      $y = $value['score'] == 'na' ? -1 : $value['score'];
      $yp = $prList[$aid];
      $time_stamp = $value['change'];
      $id = query_pre_exits( $uid, $aid, $lib_his_id, $dba );
      if( $id > 0 ){
        if( $time_stamp != 0 ){
           $dba->_execute("UPDATE `prediction` SET `y`= '". $y ."',`time_stamp` = '". $time_stamp ."' WHERE `id`='{$id}}'");
        }
      }else{
        $dba->_execute(
          'INSERT INTO prediction VALUE (0,:uid, :aid, :y, :yp, :lib_his_id, :time_stamp )',
          array(
            ':uid' => $uid,
            ':aid' => $aid,
            ':y' => $y,
            ':yp' => $yp,
            ':lib_his_id' => $lib_his_id,
            ':time_stamp' => $time_stamp
          )
        );
      }
    }
    echo json_encode(array('status'=>'success','msg'=>'feedback finish.'));
  }else{
    echo json_encode(array('status'=>'fail','msg'=>'feedback parameter missing'));
  }

function query_pre_exits( $uid, $aid, $his_id, $dba ){
  $query = $dba->_query("SELECT * from prediction where `lib_his_id`=".$his_id." and `aid`='".$aid."' and `uid`='".$uid."'");
  $data = $query->fetch(PDO::FETCH_ASSOC);
  return $data['id'];
}
?>