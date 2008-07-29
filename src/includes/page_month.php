<?php

function rawman_listmonth(&$raws) {
	$days = array();
	foreach ($raws as $raw) {
		if (preg_match('/(\d{4})(\d{2})_(\d{5})/', $raw, $m)) {
			if (!isset($days[$m[1].$m[2]])) $days[$m[1].$m[2]] = array($m[2], 0);
			$days[$m[1].$m[2]][1]++;
		}
	}
	ksort($days);
	$item = 1;
	foreach ($days as $day => $data) {
		$content .=
			'<div style="float: left;">'.
			'<a href="'.RM_WEB.'/index.php/day/all/'.$day.'">'.
			'<img src="'.RM_WEB.'/index.php/stack/all/'.$day.'" border="0" /></a><br />'.
			'Dzień: '.$data[0].', liczba zdjęć: '.$data[1].
			'</div>';
		$item++;
	}
	return $content;
}

function _rm_page_month($page) {

	$arr_month = array (
		'01' => 'Styczeń',
		'02' => 'Luty',
		'03' => 'Marzec',
		'04' => 'Kwiecień',
		'05' => 'Maj',
		'06' => 'Czerwiec',
		'07' => 'Lipiec',
		'08' => 'Sierpień',
		'09' => 'Wrzesień',
		'10' => 'Październik',
		'11' => 'Listopad',
		'12' => 'Grudzień'
	);

	$year  = substr($page, 0, 2);
	$month = substr($page, 2, 2);

	$ereg  = '[0-9]{6}_[0-9]{5}';
	$raws  = array();
	foreach(rmconf('rawdir') as $udir => $dir) {
		if (!is_dir($dir)) continue;
		$raws = array_merge($raws, rawman_readdir(sprintf('%s20%02d_%02d/', $dir, $year, $month), $ereg.'.nef'));
	}

	echo rawman_html('month', array(
		'skins'   => RM_WEB .'/skins',
		'imgdir'  => RM_WEB.'/index.php/image',
		'content' => rawman_listmonth($raws),
		'header'  => $arr_month[$month] .' 20'. $year,
		'footer'  =>
			RetIf(!IsEmpty($_SESSION['bookmark']), sprintf('&raquo; <a href="'.RM_WEB.'/index.php/bookmark/all">%s</a> (%d) &laquo; ',
				'Wyświetl ulubione', count($_SESSION['bookmark'])
			))
	));
}

?>
