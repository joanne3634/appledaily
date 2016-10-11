<?
/*-------------------------------------------------------------\

	query_aid_list ( 
		TOTAL_ARTICLES, 	// 一次測驗需要幾篇
		USER_THRESHOLD,  	// 一篇文章被做過的次數上限
		FB_ID  				// 使用者 FB_ID
	):  AID_LIST 			// 回傳本次測驗編號
	
	Article Pool 的規則
		1. user 沒做過 &
	 	2. counter >= 0
	→ random 選擇 10 篇 random order

	Update Pool 的規則
	 	1. 如果 pool 裡沒有 user 沒做過的 or
	 	2. 所有的「非 -1」counter都 > 10
	→ 開 aid 最大 (最近的) 10 篇 (即 -1 → 0)

\-------------------------------------------------------------*/
	require_once 'lib/mysql.php';

	$dba = new MYSQL\Accessor();
	$TOTAL_ARTICLES = $_POST['TOTAL_ARTICLES'];
	$USER_THRESHOLD = $_POST['USER_THRESHOLD'];
	$FB_ID = $_POST['FB_ID'];
	$AID_LIST = array();

	
	$user_done_aid = query_user_done_aid( $FB_ID , $dba ); 
	// print_r( $user_done_aid );	
	$user_done_aid_list = implode( "','",array_keys($user_done_aid) );

	/* get 使用者在 article pool 中能測驗 aid */
	$query = $dba->_query("SELECT `aid` FROM `article` WHERE `pending`= 0 and `count` >= 0 and `count` < ".$USER_THRESHOLD." and `aid` not in ('{$user_done_aid_list}') ORDER BY RAND() limit ". $TOTAL_ARTICLES); 
	while( $article_pool = $query->fetch(PDO::FETCH_ASSOC) ) array_push( $AID_LIST, $article_pool['aid'] );
	// print_r( $AID_LIST );
	
	/* 若能測驗的 aid < 本次測驗所需數目 → 擴大 article pool */
	$article_pool_num = $query->rowCount();	
	// print( "\n". $article_pool_num );
	if( $article_pool_num < $TOTAL_ARTICLES ){
		$query = $dba->_query("SELECT * FROM `article` WHERE `pending`= 0 and `count` < 0 and `aid` not in ('{$user_done_aid_list}') ORDER BY `article`.`aid` DESC limit ". ( $TOTAL_ARTICLES - $article_pool_num ));
		while( $article_pool = $query->fetch(PDO::FETCH_ASSOC) ) array_push($AID_LIST, $article_pool['aid']);
	}
	// print_r( $AID_LIST );
	if( count($AID_LIST) != $TOTAL_ARTICLES ) {
		$article_pool_list = implode( "','",$AID_LIST );
		$query = $dba->_query("SELECT * FROM `article` WHERE `pending`= 0 and `aid` not in ('{$article_pool_list}') ORDER BY RAND() limit ". ( $TOTAL_ARTICLES - count($AID_LIST) ));
		while( $article_pool = $query->fetch(PDO::FETCH_ASSOC) ) array_push($AID_LIST, $article_pool['aid']);
	}
	echo json_encode(array('status'=>'success','msg'=> $AID_LIST ));

	function query_user_done_aid( $FB_ID, $dba ){
		$query = $dba->_query('SELECT `Article_done` FROM `fb_id` WHERE `fb_id`= :fb_id', array( ':fb_id' => $FB_ID ));
		$article_data = $query->fetch(PDO::FETCH_ASSOC);
		$article = json_decode( $article_data['Article_done'],true );
		if( $article == 0 ){ $article = array(); } 
		return $article;
	}
?>
