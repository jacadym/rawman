<?php

function rawman_listday(&$raws) {
	$count = 0;
	foreach ($raws as $udir => $arr)
	foreach ($arr as $raw) {
		$pic = rawman_filename($raw);
		$el  = rawman_elem($pic);
		$opt = rawman_convparams(
			rawman_mkdir(array(rawman_getpicdir($pic), 'param')) . $pic .'.txt',
			array('rating' => 0, 'coloring' => 'none')
		);

		$class = 'thumbnail';
		if ($opt['coloring'] != 'none') {
			$class .= ' '.$opt['coloring'];
		}
		$title    = sprintf('20%02d-%02d-%02d, #%d', $el['year'], $el['month'], $el['day'], $el['number']);
		$content .= sprintf(
		'<li class="%s" id="thumb-%s">'.
			'<img src="%s/index.php/thumb/%s/%s.png" onclick="selImg(\'%s\',\'%s\')" title="%s" />'.
			'<br clear="both"/>%s'.
			'<br clear="both"/>%s'.
		'</li>',
			$class, $pic,
			RM_WEB, $udir, $pic, $pic, $udir, $title,
			$pic,
			rawman_rating($opt['rating'], false)
		);
		$count++;
	}
	return '<ul class="thumbnails" style="width: '.(130 * $count).'px;">'.$content.'</ul>';
}

function _rm_page_day($page) {

	$year  = substr($page, 0, 2);
	$month = substr($page, 2, 2);

	$ereg = $page.'_[0-9]{5}\.';
	$raws  = array();
	foreach(rmconf('rawdir') as $udir => $dir) {
		if (!is_dir($dir)) continue;
		$raws[$udir] = rawman_readdir(sprintf('%s20%02d_%02d/', $dir, $year, $month), $ereg);
	}

	echo rawman_html('day', array(
		'skins'  => RM_WEB .'/skins',
		'imgdir' => RM_WEB.'/index.php/image',
		'thumbs' => rawman_listday($raws)
	));
}

?>
