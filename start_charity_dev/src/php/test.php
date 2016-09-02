<?php

$fbid = $_GET['fbid']; 
$uid = $_GET['uid'];
$aid = $_GET['aid']; 
$score = $_GET['score'];
$timestamp = $_GET['timestamp'];
$method = $_GET['train_method'];

$fb_fav_like = $_GET['fb_fav_like'] ? '&fb_fav_like':'';
$fb_cat = $_GET['fb_cat'] ? '&fb_cat':'';
$fb_catlist = $_GET['fb_catlist'] ? '&fb_catlist':'';
$fb_str = $fb_fav_like . $fb_cat . $fb_catlist; 

print("build_mapping_table.php:\n");
print_r( $response = file_get_contents('http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/php/build_mapping_table.php/update/'.$fbid));


print("\nbuild_libfm_serial.php:\n");
print_r( $response = file_get_contents('http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/php/build_libfm_serial.php/update/'.$uid.'?'. $fb_str));

if( $method == 'uid_single_train'){
	print("\nuid_single_train:\n");

	print_r( $response = file_get_contents('http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/php/generate_libfm_train.php/uid_single_train?fbid='.$fbid.'&uid='.$uid.'&aid='.$aid.'&score='.$score.'&timestamp='.$timestamp. $fb_str ));
}
if($method == 'uid_set_train'){
	print("\nuid_set_train:\n");

	print_r( $response = file_get_contents('http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/php/generate_libfm_train.php/uid_set_train?fbid='.$fbid.'&uid='.$uid.$fb_str ));
}
print("\ngenerate_libfm_test.php:\n");

print_r($response = file_get_contents('http://mmnet.iis.sinica.edu.tw/~joanne3634/start_charity_dev/src/php/generate_libfm_test.php?'.$fb_str.'&fbid='.$fbid.'&uid='.$uid));

print("\nlibfm.R:\n");

exec('Rscript /home/helen/recommender/libfm.R "'.$uid.'" 2>&1',$output);

print_r( $output );
print_r( $uid_lists = json_decode(file_get_contents('/home/helen/recommender/data/libfm/out_data/libfm_result_'.$uid.'.json'),true) );

// /home/helen/recommender/ata/libfm/out_data/libfm_result_[uid].json



?>