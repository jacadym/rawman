<?php

function _rm_page_main() {

	$content = '';
	$months  = array();
	foreach(rmconf('rawdir') as $udir => $dir) {
		if (!is_dir($dir)) continue;
		$months = array_merge($months, rawman_readdir($dir, '[0-9]{4}_[0-9]{2}'));
	}
	sort($months);
	$years  = array();
	foreach ($months as $month) {
		if (preg_match('/\d{2}(\d{2})_(\d{2})/', $month, $m)) {
			$years[$m[1]][] = $m[2];
		}
	}
	$content = '<table border="0">';
	foreach ($years as $year => $yd) {
		$content .= '<tr><td colspan="4" class="year">20'.$year."</td></tr>\n";
		for ($m = 1; $m <= 12; $m++) {
			if (($m %3) == 1) $content .= '<tr><td width="50"></td>';
			$content .= '<td>';
			$month = sprintf('%02d', $m);
			if (in_array($month, $yd)) {
				$link = sprintf('%02d%02d', $year, $month);
				$content .=
				'<div>'.
					'<a href="'.RM_WEB.'/index.php/month/all/'.$link.'"> '.
					'<img src="'.RM_WEB.'/index.php/stack/all/'.$link.'" border="0" />'.
					'<br clear="all" />'.
					$month.
					'</a>'.
				"</div>\n";
			}
			$content .= '</td>';
			if (($m % 3) == 0) $content .= "</tr>\n";
		}
	}
	$content .= '</table>';

	echo rawman_html('main', array(
		'skins'   => RM_WEB .'/skins',
		'content' => $content,
		'footer'  =>
			RetIf(!IsEmpty($_SESSION['bookmark']), sprintf('&raquo; <a href="'.RM_WEB.'/index.php/bookmark/all">%s</a> (%d) &laquo; ',
				'Wyświetl ulubione', count($_SESSION['bookmark'])
			))
	));
}

?>
