<?php

function rawman_listday(&$raws) {
	foreach ($raws as $raw) {
		$pic   = basename($raw, '.nef');
		$el    = rawman_elem($pic);

		$opt   = rawman_convparams(
			rawman_mkdir(array(RM_PIC .'20'.$el['year'].'_'.$el['month'], 'param')) . $pic .'.txt',
			array('rating' => 0, 'coloring' => 'none')
		);

		$class = 'thumbnail';
		if ($opt['coloring'] != 'none') {
			$class .= ' '.$opt['coloring'];
		}
		$content .= sprintf(
			'<li class="%s" id="thumb-%s">'.
				'<img src="%s/index.php/thumb/%s.png" onclick="selImg(\'%s\')" title="%s" />'.
				'<br clear="both"/>%s'.
				'<br clear="both"/>%s'.
			'</li>',
				$class, $pic,
				RM_WEB, $pic, $pic,
				sprintf('20%02d-%02d-%02d, #%d', $el['year'], $el['month'], $el['day'], $el['number']),
				$pic,
				rawman_rating($opt['rating'], false)
		);
	}
	return '<ul class="thumbnails" style="width: '.(130 * count($raws)).'px;">'.$content.'</ul>';
}

function _rm_page_day($page) {
	$dir  = sprintf('%s20%02d_%02d/', RM_RAW, substr($page, 0, 2), substr($page, 2, 2));
	$ereg = $page.'_[0-9]{5}';
	$raws = rawman_readdir($dir, $ereg.'.nef');

	echo rawman_html('day', array(
		'skins'  => RM_WEB .'/skins',
		'imgdir' => RM_WEB.'/index.php/image',
		'thumbs' => rawman_listday($raws)
	));
}

?>
