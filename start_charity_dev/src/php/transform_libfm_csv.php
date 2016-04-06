<?php
require_once 'lib/common.php';
require_once 'lib/log.php';

if (!isset($_SESSION)) {
	START_SESSION($SESSION_TIME);
}

$libfm_filename = $DIR_LOGS_ROOT . '/libfm_objects';
if ($handle = opendir($libfm_filename)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			$libfm_objects = json_decode(file_get_contents($libfm_filename . '/' . $entry), true);

			foreach ($libfm_objects['DATA'] as $uniqId => $item) {
				$dataset = '';
				$rating = '';

				$dataset .= addSpace('fb_id#' . $libfm_objects['FB_ID'] . ':1');
				foreach ($item['USER'] as $userIdx => $userValue) {
					if ($userValue != 0) {
						$dataset .= addSpace('user#' . $userIdx . ':1');
					}
				}

				$dataset .= addSpace('fb_friends:' . $item['FB']['friends']);
				$dataset .= addSpace('fb_gender-' . $item['FB']['gender'] . ':1');

				foreach ($item['FB']['favorite'] as $fb_id => $fb_name) {
					$dataset .= addSpace('fb_favorite' . $fb_id . ':1');
				}
				foreach ($item['FB']['like_id'] as $fb_id => $fb_name) {
					$dataset .= addSpace('fb_like_id' . $fb_id . ':1');
				}
				foreach ($item['FB']['like_category'] as $fb_id => $fb_name) {
					$dataset .= addSpace('fb_like_category' . str_replace(' ', '_', $fb_id) . ':' . $fb_name);
				}
				foreach ($item['FB']['like_category_list'] as $fb_id => $fb_name) {
					$dataset .= addSpace('fb_like_category_list' . str_replace(' ', '_', $fb_id) . ':' . $fb_name);
				}

				foreach ($item['ROUND'] as $aid => $score) {
					$rating .= addChangeLine(addSpace($score) . addSpace('#' . $libfm_objects['FB_ID']) . addSpace($aid));
				}

				$new_dir_logs = '../../db_libfm';
				if (!file_exists($new_dir_logs)) {
					mkdir($new_dir_logs, 0777, true);
				}

				$data_filename = $uniqId . '_data.txt';
				if (file_exists($data_filename)) {
					unlink($data_filename);
				}

				file_put_contents($new_dir_logs . '/' . $data_filename, $dataset);

				$rating_filename = $uniqId . '_rating.txt';
				if (file_exists($rating_filename)) {
					unlink($rating_filename);
				}

				file_put_contents($new_dir_logs . '/' . $rating_filename, $rating);
			}

		}
	}
	closedir($handle);
}

function addSpace($string) {
	return $string . " ";
}
function addChangeLine($string) {
	return $string . "\n";
}

?>