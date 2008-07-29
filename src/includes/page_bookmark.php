<?php

function rawman_bookmark_list() {
	if (is_array($_SESSION['bookmark'])) {
		$list = $_SESSION['bookmark'];
		ksort ($list);
		$content .= join ("\n", array_unique(array_keys($list))) ."\n";
	}
	echo $content;
}

function rawman_listbookmark() {
	$count = 0;
	if (is_array($_SESSION['bookmark'])) {
		foreach ($_SESSION['bookmark'] as $img => $udir) {
			$pic = rawman_filename($img);
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
	}
	return '<ul class="thumbnails" style="width: '.(130 * $count).'px;">'.$content.'</ul>';
}

function _rm_page_bookmark() {
	echo rawman_html('day', array(
		'skins'  => RM_WEB .'/skins',
		'imgdir' => RM_WEB.'/index.php/image',
		'thumbs' => rawman_listbookmark()
	));
}

?>
