<?php

function _rm_page_stack($page) {
	$pagedir  = rawman_getpicdir($page);
	$stackdir = rawman_mkdir(array($pagedir, 'stack'));

	if (strlen($page) == 4) {
		// Month
		$ereg = '[0-9]{6}_[0-9]{5}';
	}
	else {
		// Day
		$ereg = $page.'_[0-9]{5}';
	}	
	
	$items = rawman_readdir($stackdir, $ereg.'.png', true);
	if (count($items)) {
		// Exists
		$stackfile = $items[rand(0, count($items) - 1)];
	}
	else {
		// Create stack file
		$year  = substr($page, 0, 2);
		$month = substr($page, 2, 2);
		$raws  = array();
		foreach(rmconf('rawdir') as $udir => $dir) {
			if (!is_dir($dir)) continue;
			$raws = array_merge($raws, rawman_readdir(sprintf('%s20%02d_%02d/', $dir, $year, $month), $ereg, true));
		}
		if (count($raws)) {
			// Random
			$paramdir  = rawman_mkdir(array($pagedir, 'param'));
			$rawfile   = $raws[rand(0, count($raws) - 1)];
			$filename  = rawman_filename($rawfile);
			$stackfile = $stackdir . $filename . '.png';
			rawman_createstack(
				$rawfile,
				$stackfile,
				rawman_convparams($paramdir . $filename .'.txt')
			);
		}
	}
	rawman_showpicture($stackfile);
}

?>
