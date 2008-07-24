<?php

function _rm_page_main_1() {

	$content = '';
	if (is_dir(RM_RAW)) {
		$months = rawman_readdir(RM_RAW, '[0-9]{4}_[0-9]{2}');
		$item = 1;
		foreach ($months as $month) {
			if (preg_match('/\d{2}(\d{2})_(\d{2})/', $month, $match)) {
				$link = $match[1].$match[2];
				$content .=
					'<div style="float: left;">'.
					'<a href="'.RM_WEB.'/index.php/month/'.$link.'"> '.
					'<img src="'.RM_WEB.'/index.php/stack/'.$link.'" border="0" />'.
					'<br clear="all" />'.
					'<span style="text-align: center;">'.$month.'</span>'.
					'</a>'.
					'</div>';
				if ($item % 4 == 0) $content .= "<br />\n";
				$item++;
			}
		}
	}
	else {
		$content .= 'Not found dir: '.RM_RAW;
	}

	echo rawman_html('main', array(
		'skins'   => RM_WEB .'/skins',
		'content' => $content,
		'footer'  =>
			RetIf(!IsEmpty($_SESSION['bookmark']), sprintf('&raquo; <a href="'.RM_WEB.'/index.php/bookmark">%s</a> &laquo; ',
				'Wyświetl ulubione'
			))
	));
}

function _rm_page_main() {

	$content = '';
	if (is_dir(RM_RAW)) {
		$months = rawman_readdir(RM_RAW, '[0-9]{4}_[0-9]{2}');
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
						'<a href="'.RM_WEB.'/index.php/month/'.$link.'"> '.
						'<img src="'.RM_WEB.'/index.php/stack/'.$link.'" border="0" />'.
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
	}
	else {
		$content .= 'Not found dir: '.RM_RAW;
	}

	echo rawman_html('main', array(
		'skins'   => RM_WEB .'/skins',
		'content' => $content,
		'footer'  =>
			RetIf(!IsEmpty($_SESSION['bookmark']), sprintf('&raquo; <a href="'.RM_WEB.'/index.php/bookmark">%s</a> &laquo; ',
				'Wyświetl ulubione'
			))
	));
}

?>
