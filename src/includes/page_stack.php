<?php

function _rm_page_stack($page) {
	$stack = '';

	$pagedir  = sprintf('%s20%02d_%02d', RM_PIC, substr($page, 0, 2), substr($page, 2, 2));
	$stackdir = rawman_mkdir(array($pagedir, 'stack'));
	$paramdir = rawman_mkdir(array($pagedir, 'param'));

	if (strlen($page) == 4) {
		// Miesięczna
		$ereg = '[0-9]{6}_[0-9]{5}';
	}
	else {
		// Dziennna
		$ereg = $page.'_[0-9]{5}';
	}	
	
	$items = rawman_readdir($stackdir, $ereg.'.png');
	if (count($items)) {
		$stack = basename($items[rand(0, count($items) - 1)], '.png');
	}
	else {
		$rawdir = sprintf('%s20%02d_%02d/', RM_RAW, substr($page, 0, 2), substr($page, 2, 2));
		$raws = rawman_readdir($rawdir, $ereg.'.nef');
		if (count($raws)) {
			// losujemy
			$stack = basename($raws[rand(0, count($raws) - 1)], '.nef');
			rawman_createstack(
				$rawdir . $stack .'.nef',
				$stackdir . $stack .'.png',
				rawman_convparams($paramdir . $stack .'.txt')
			);
		}
	}
	rawman_showpicture($stackdir . $stack .'.png');
}

?>
