<?
	require_once 'lib/mysql.php';
	require_once 'lib/common.php';
  	require_once 'lib/log.php';

	if (!isset($_SESSION)) START_SESSION($SESSION_TIME);

	if (isset($_POST['FB_ID'])) {

		/*==========  libfm data saving  ==========*/

		// Initial
		$new_dir_logs = $DIR_LOGS_ROOT . '/libfm_objects';
		if(!file_exists($new_dir_logs)) mkdir($new_dir_logs, 0777, true);
		$filename = $_POST['FB_ID'] . '_libfm.json';

		$libfm = array();
		$libfm = json_decode(file_get_contents($new_dir_logs . '/' . $filename),true);

		if( !isset( $libfm['FB_ID'] )){ $libfm['FB_ID'] = $_POST['FB_ID']; }
		if( !isset( $libfm['SUBSCRIBING'] )){ $libfm['SUBSCRIBING'] = 0; }
		if( !isset($libfm['DATA'] )){ $libfm['DATA'] = array(); }
		if( !isset($libfm['USER'] )){ $libfm['USER'] = array(); }
		if( !isset($libfm['FB'] )){ $libfm['FB'] = array(); }

		// RecordSubscribeInLibfm
		if( isset( $_POST['SUBSCRIBING'] )){ $libfm['SUBSCRIBING'] = intval($_POST['SUBSCRIBING']); }

		// RecordLibfm 有更新問卷或是重新做測驗
		if( !isset( $libfm['DATA'][$_POST['UNIQ_ID']] ) && isset($_POST['UNIQ_ID']) ){

			$data = array();
			$data['UNIQ_ID'] = $_POST['UNIQ_ID'];
			$data['TIME_RECORD'] = json_decode($_POST['timeRecording'], true);

			// Update UNIQ_ID, FB_ID in DATABASE
			$dba = new MYSQL\Accessor('localhost','appledaily','joanne3634','369369');
	 		$dba->_execute(
			    'INSERT INTO uniq_id VALUE (0,:uniqId,:fbId)',
			    array(
			        ':uniqId' => $_POST['UNIQ_ID'],
			        ':fbId' => $_POST['FB_ID']
			    )
			);
			$dba->_execute(
			    'INSERT INTO fb_id VALUE (0,:fbId)',
			    array(
			        ':fbId' => $_POST['FB_ID']
			    )
			);

			/*==========  questionaire saving  ==========*/

			$data['USER'] = array();

				/*=============== 初始化問券內容(儲存所有題目包含0) ==================*/
				foreach( $QUESTIONAIRE_DATASET as $key => $value ){
					foreach( $value as $keyOfvalue => $item ){
						$data['USER'][$key.'-'.$keyOfvalue] = 0;
					}
				}
				$data['USER']['charityWilling'] = -1;

				/*=============== 儲存問券內容 ==================*/
				$USER_QUESTIONAIRE = json_decode($_POST['USER_QUESTIONAIRE'],true);
				if( $USER_QUESTIONAIRE['gender'] == 'na'){ // 沒有做問卷從舊資料抓
					$data['USER'] = $libfm['USER'];
				}else{
					foreach ( $USER_QUESTIONAIRE as $key => $value ) {
						if( $key == 'charityWilling' ){
							$data['USER']['charityWilling'] = intval($value[0]);
						}else{
							foreach( $value as $question_item ){
								if( $question_item != ''){
									$data['USER'][$key.'-'.$question_item] = 1;
								}
							}
						}
					}
				}
			$libfm['USER'] = $data['USER'];

			/*==========  facebook saving  ==========*/

			$data['FB'] = array();

			$fb_dir_logs = $DIR_LOGS_ROOT . '/facebook_objects';
			$fb_likes_filename = $_POST['FB_ID'] . '_likes.json';
			$fb_me_filename = $_POST['FB_ID'] . '_me.json';
			$fb_friends_filename = $_POST['FB_ID'] . '_friends.json';

				/*==========  facebook friends saving  ==========*/

				$facebook_friends_objects = json_decode(file_get_contents($fb_dir_logs . '/' . $fb_friends_filename),true);
				$data['FB']['friends'] = $facebook_friends_objects['summary']['total_count'];

				/*==========  facebook me saving  ==========*/

				$facebook_me_objects = json_decode(file_get_contents($fb_dir_logs . '/' . $fb_me_filename),true);

				// male = 0, female = 1, other = 2
				$data['FB']['gender'] = ( $gender = array_search( $facebook_me_objects['gender'], $FACEBOOK_GENDER )) === FALSE ? 2 : $gender;
				$data['FB']['favorite'] = store_fb_id_by_column($facebook_me_objects);
				$data['FB']['like_id'] = array();
				$data['FB']['like_category'] = array();
				$data['FB']['like_category_list'] = array();

				/*==========  facebook likes saving  ==========*/
				$facebook_likes_objects = json_decode(file_get_contents($fb_dir_logs . '/' . $fb_likes_filename),true);

				foreach ( $facebook_likes_objects as $key => $value) {
					$data['FB']['like_id']['#'.$value['id']] = 1;

					if( !empty( $data['FB']['like_category']['#'.$value['category']] )) {
						$data['FB']['like_category']['#'.$value['category']] ++;
					}else{
						$data['FB']['like_category']['#'.$value['category']] = 1;
					}

					if( isset($value['category_list'] )){
						foreach( $value['category_list'] as $key_cl => $val_cl ){
							if( !empty( $data['FB']['like_category_list']['#'.$val_cl['name']] )) {
								$data['FB']['like_category_list']['#'.$val_cl['name']] ++;
							}else{
								$data['FB']['like_category_list']['#'.$val_cl['name']] = 1;
							}
						}
					}
				}
			$libfm['FB'] = $data['FB'];

			/*==========  round saving  ==========*/

			$data['ROUND'] = array();
			$ROUND_RESULT = json_decode($_POST['ROUND_RESULT'],true);

			foreach ( $ROUND_RESULT as $key => $value ) {
				$data['ROUND']['#'.$value['aid']] = $value['score'];
			}

			$libfm['DATA'][$_POST['UNIQ_ID']] = $data;

		}

		file_put_contents($new_dir_logs . '/' . $filename, json_encode($libfm));
		echo json_encode(array('status'=>'success'));
	} else {
		$msg = 'the variable FB_ID not exist';
    	EXCEPTION_RECORDING($msg, $_POST['UNIQ_ID']);
		echo json_encode(array('status'=>'fail', 'message'=>$msg));
	}

	function store_fb_id_by_column( $fb_object ){
		$save = array();
		foreach ( $fb_object as $column_name => $fb_item ){
			foreach( $fb_item as $value ){
				if( isset($value['id']) && isset($value['name']) ){
					$save['#'.$value['id']] = 1;
				}else{
					break;
				}
			}
		}
		return $save;
	}
?>