<?php

// yymmdd_xxxxx.png
function _rm_page_thumb($pic) {
	if (preg_match('/(\d{2})(\d{2})(\d{2})_(\d{5})/', $pic, $m)) {
		$year   = $m[1];
		$month  = $m[2];
		$day    = $m[3];
		$number = $m[4];
		
		$monthdir = rawman_getpicdir($pic);
		$thumbdir = rawman_mkdir(array($monthdir, 'thumb'));
		$paramdir = rawman_mkdir(array($monthdir, 'param'));

		$thumbfile = $thumbdir . $pic;
		$opt       = array(
			'year'   => '20'.$year,
			'number' => $number
		);
		if (!is_file($thumbfile)) {
			$filename = rawman_filename($pic);
			$rawfile  = rawman_getrawfile($pic);
			rawman_createthumb(
				$rawfile,
				$thumbfile,
				rawman_convparams($paramdir . $filename .'.txt', $opt)
			);
		}
		rawman_showpicture($thumbfile);
	}
}

?>
