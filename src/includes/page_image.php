<?php

function rawman_bookmark($pic, $act = 'add') {
	if (!isset($_SESSION['bookmark'])) $_SESSION['bookmark'] = array();
	if ($act == 'add') {
		$_SESSION['bookmark'][$pic] = rmconf('elem-dir');
	}
	elseif ($act == 'del') {
		unset($_SESSION['bookmark'][$pic]);
	}
}

function rawman_genimage($pic) {
	$dir = rawman_getpicdir($pic);
	$par = rawman_mkdir(array($dir, 'param')) . $pic .'.txt';
	$img = rawman_mkdir(array($dir, 'image', 'orig')) . $pic .'.jpg';
	$opt = rawman_convparams($par, array('rating' => 0, 'coloring' => 'none'));
	$raw = rawman_getrawfile($pic);

	$opt['dcraw']    = preg_replace('/\-h/', '', $opt['dcraw']);
	$opt['dcraw']   .= ' -q 3';
	$opt['cnvpost'] .= ' -quality 95';

	rawman_createjpg($raw, $img, $opt);
}

function rawman_colorimage($pic, $color) {
	$dir = rawman_getpicdir($pic);
	$par = rawman_mkdir(array($dir, 'param')) . $pic .'.txt';
	$opt = rawman_convparams($par);
	$opt['coloring'] = $color;
	@unlink($par);
	rawman_convparams($par, $opt);
}

function rawman_rateimage($pic, $rate) {
	$dir = rawman_getpicdir($pic);
	$par = rawman_mkdir(array($dir, 'param')) . $pic .'.txt';
	$opt = rawman_convparams($par);
	$opt['rating'] = $rate;
	@unlink($par);
	rawman_convparams($par, $opt);
}

function rawman_editimage($conv, $pic) {
	$el  = rawman_elem($pic);
	$dir = rawman_getpicdir($pic);
	$img = rawman_mkdir(array($dir, 'image', IMAGE_SIZE)) . $pic .','. $conv .'.jpg';
	$opt = array(
		'year'   => '20'.$el['year'],
		'number' => $el['number']
	);
	$raw  = rawman_getrawfile($pic);
	$info = pathinfo($raw);
	if (!is_file($image)) {
		/*
		** Analiza parametrów
		*/
		list($par_ev, $par_balance, $par_brightness, $par_gamma, $par_rotate, $par_noise) = split(',', $conv);
		$opt['dcraw'] .= rawman_get_dcraw_wb($par_balance);
		if (!IsEmpty($par_noise)) {
			$opt['dcraw'] .= ' -n '.$par_noise;
		}
		$opt['dcraw']   .= ' -h -b '. RetDefault($par_brightness, '1.33');
		if (in_array($par_rotate, array('left','right'))) {
			if ($info['extension'] == 'jpg') {
				$opt['cnvpre'] .= ' -rotate '. ($par_rotate == 'left' ? 270 : 90);
			}
			else {
				$opt['dcraw'] .= ' -t '. ($par_rotate == 'left' ? 270 : 90);
			}
		}
		$opt['cnvpre']  .= ' -shave 4x4 -gamma '. RetDefault($par_gamma, '1.15');
		$opt['cnvpost'] .= ' -unsharp 3x3+0.3+0';
		rawman_createimage($raw, $img, $opt);
	}
	rawman_showpicture($img);
}

function rawman_applyimage($conv, $pic) {

	$dir = rawman_getpicdir($pic);
	$img = rawman_mkdir(array($dir, 'image', IMAGE_SIZE)) . $pic .'.jpg';
	$stk = rawman_mkdir(array($dir, 'stack')) . $pic .'.png';
	$thu = rawman_mkdir(array($dir, 'thumb')) . $pic .'.png';
	$par = rawman_mkdir(array($dir, 'param')) . $pic .'.txt';
	$opt = rawman_convparams($par);

	@unlink($par);
	@unlink($stk);
	@unlink($img);
	@unlink($thu);

	list($par_ev, $par_balance, $par_brightness, $par_gamma, $par_rotate, $par_noise) = split(',', $conv);

	$opt['dcraw']   = '';
	$opt['cnvpre']  = '';
	$opt['cnvpost'] = '';

	$raw  = rawman_getrawfile($pic);
	$info = pathinfo($raw);

	$opt['dcraw']  .= rawman_get_dcraw_wb($par_balance).' -h -b '. RetDefault($par_brightness, '1.33');
	if (in_array($par_rotate, array('left','right'))) {
		if ($info['extension'] == 'jpg') {
			$opt['cnvpre'] .= ' -rotate '. ($par_rotate == 'left' ? 270 : 90);
		}
		else {
			$opt['dcraw'] .= ' -t '. ($par_rotate == 'left' ? 270 : 90);
		}
	}
	$opt['cnvpre']  .= ' -shave 4x4 -gamma '. RetDefault($par_gamma, '1.15');
	$opt['cnvpost'] .= ' -unsharp 3x3+0.3+0';
	$opt['wb']       = $par_balance;

	rawman_convparams($par, $opt);
}

function rawman_infobox($pic) {
	$dir = rawman_getpicdir($pic);
	$par = rawman_mkdir(array($dir, 'param')) . $pic .'.txt';
	$img = rawman_mkdir(array($dir, 'image', 'orig')) . $pic .'.jpg';
	$opt = rawman_convparams($par, array('rating' => 0, 'coloring' => 'none'));

	echo
		'&raquo; <a onclick="rmSendReq(\''.rmconf('elem-dir').'/box\')">Edytuj zdjęcie</a> &laquo;'.
		'<br />'.
		(isset($_SESSION['bookmark'][$pic]) ?
			sprintf('&raquo; <a onclick="rmSendReq(\''.rmconf('elem-dir').'/del\')">Jest w ulubionych (%d) - Usuń</a> &laquo;',
				count($_SESSION['bookmark'])
			)
		:
			'&raquo; <a onclick="rmSendReq(\''.rmconf('elem-dir').'/add\')">Dodaj do ulubionych</a> &laquo;'
		).
		RetIf(!is_file($img),
			'<br />'.
			'&raquo; <a onclick="rmSendReq(\''.rmconf('elem-dir').'/gen\')">Wygeneruj JPG</a> &laquo;'
		).
		'<br /><br />'.
		rawman_coloring($opt['coloring'], true).
		'<br />'.
		rawman_rating($opt['rating'], true).
		'<br />'.
		join('<br />', rawman_readexif($pic));
}

function rawman_editbox($pic) {
	// Reading params and settting form fields
	$par = rawman_mkdir(array(rawman_getpicdir($pic), 'param')) . $pic .'.txt';
	$opt = rawman_convparams($par, array(
		'rating'   => 0,
		'coloring' => 'none',
		'wb'       => 'camera',
		'rotate'   => 'none'
	));
	if (preg_match('/\-b\s+(\d\.\d+)\b/', $opt['dcraw'], $m)) {
		_fsine(array('brightness' => $m[1]));
	}
	if (IsEmpty($opt['wb']) && preg_match('/\-a\b/', $opt['dcraw'], $m)) {
		$opt['wb'] = 'auto';
	}
	if (preg_match('/\-gamma\s+(\d\.\d+)\b/', $opt['cnvpre'], $m)) {
		_fsine(array('gamma' => $m[1]));
	}
	if (preg_match('/\-t\s+(\d+)\b/', $opt['dcraw'], $m)) {
		$opt['rotate'] = $m[1] == 270 ? 'left' : 'right';
	}
	elseif (preg_match('/\-rotate\s+(\d+)\b/', $opt['cnvpre'], $m)) {
		$opt['rotate'] = $m[1] == 270 ? 'left' : 'right';
	}

	$arr_ev = array(
		'-2.00', '-1.66', '-1.50', '-1.33', '-1.00', '-0.66', '-0.50', '-0.33',
		'0.00',
		'+0.33', '+0.50', '+0.66', '+1.00', '+1.33', '+1.50', '+1.66', '+2.00',
	);

	$arr_high = array (
		0 => '0 - White',
		1 => '1 - Pink',
		3 => '3',
		4 => '4',
		5 => '5',
		6 => '6',
		7 => '7',
		8 => '8',
		9 => '9',
	);

	_fsine(array(
		'ev'     => '+0.33',
		'rotate' => $opt['rotate'],
	));

echo '
<form name="editForm">'.
	CreateHiddenField('balance', $opt['wb']).
	rawman_wb($opt['wb'], true).
'<p style="clear:both;"></p>
<ul>
<li>Ekspozycja: '.CreateSelectField('ev', $arr_ev, 0).'EV</li>
<li>Jasność: '.CreateInputText('brightness', 5).'</li>
<li>Gamma: '.CreateInputText('gamma', 5).'</li>
<li>Ziarno: '.CreateInputText('noise', 5).'</li>
<li>Odbłyski: '.CreateSelectField('highlight', $arr_high).'</li>
</ul>
'.
'<p style="clear:both;"></p>'.
	CreateHiddenField('rotate', $opt['rotate']).
	rawman_rotate($opt['rotate'], true).
'<p style="clear:both;"></p>'.
CreateButton('preview', 'View', 'onclick="editImg()"').
CreateButton('cancel', 'Cancel', 'onclick="rmSendReq(\''.rmconf('elem-dir').'/info\')"').
CreateButton('apply', 'Apply', 'onclick="applyImg()"').
'</form>';
}

// yymmdd_xxxxx.jpg
function _rm_page_image($pic, $params) {
	if (preg_match('/(\d{2})(\d{2})(\d{2})_(\d{5})/', $pic, $match)) {
		$year   = $match[1];
		$month  = $match[2];
		$day    = $match[3];
		$number = $match[4];
		
		$monthdir = rawman_getpicdir($pic);
		$imagedir = rawman_mkdir(array($monthdir, 'image', IMAGE_SIZE));
		$paramdir = rawman_mkdir(array($monthdir, 'param'));

		$imagefile = $imagedir . $pic;
		$opt       = array(
			'year'   => '20'.$year,
			'number' => $number
		);
		if (!is_file($imagefile)) {
			$filename = rawman_filename($pic);
			$rawfile  = rawman_getrawfile($pic);
			rawman_createimage(
				$rawfile,
				$imagefile,
				rawman_convparams($paramdir . $filename .'.txt', $opt)
			);
		}
		rawman_showpicture($imagefile);
	}
	else {
		$act = $pic;
		$pic = $params[0];
		switch ($act) {
		case 'info':
			rawman_infobox($pic);
			break;
		case 'box':
			rawman_editbox($pic);
			break;
		case 'edit':
			$conv = $params[0];
			$img  = $params[1];
			rawman_editimage($conv, $img);
			break;
		case 'apply':
			$conv = $params[0];
			$img  = $params[1];
			rawman_applyimage($conv, $img);
			rawman_infobox($img);
			break;
		case 'add':
			rawman_bookmark($pic, 'add');
			rawman_infobox($pic);
			break;
		case 'del':
			rawman_bookmark($pic, 'del');
			rawman_infobox($pic);
			break;
		case 'gen':
			rawman_genimage($pic);
			rawman_infobox($pic);
			break;
		case 'rate':
			$rate = $params[0];
			$pic  = $params[1];
			rawman_rateimage($pic, $rate);
			rawman_infobox($pic);
			break;
		case 'color':
			$color = $params[0];
			$pic   = $params[1];
			rawman_colorimage($pic, $color);
			rawman_infobox($pic);
			break;
		}
	}
}

?>
