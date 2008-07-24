<?php

// yymmdd_xxxxx.png
function _rm_page_thumb($pic) {
	if (preg_match('/(\d{2})(\d{2})(\d{2})_(\d{5})/', $pic, $match)) {
		$year   = $match[1];
		$month  = $match[2];
		$day    = $match[3];
		$number = $match[4];
		
		$monthdir = RM_PIC .'20'.$year.'_'.$month;
		$thumbdir = rawman_mkdir(array($monthdir, 'thumb'));
		$paramdir = rawman_mkdir(array($monthdir, 'param'));

		$thumb    = $thumbdir . $pic;
		$opt      = array(
			'year'   => '20'.$year,
			'number' => $number
		);
		if (!is_file($thumb)) {
			$rawdir = RM_RAW . '20'.$year.'_'.$month.'/';
			rawman_createthumb(
				$rawdir . basename($pic, '.png') .'.nef',
				$thumb,
				rawman_convparams($paramdir . basename($pic, '.png') .'.txt', $opt)
			);
		}
		rawman_showpicture($thumb);
	}
}

?>
