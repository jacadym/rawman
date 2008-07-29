<?php

include('config.php');
include('convert.inc.php');

include('page_bookmark.php');
include('page_day.php');
include('page_image.php');
include('page_main.php');
include('page_month.php');
include('page_stack.php');
include('page_thumb.php');

function rmconf($key, $val = null) {
	static $conf = array();
	if (!is_null($val)) $conf[$key] = $val;
	return $conf[$key];
}

function rawman_pathinfo($full = true) {
	if (empty($_SERVER['PATH_INFO'])) return array();

	if ($full || !($pos = strpos($_SERVER['PATH_INFO'], '/', 1))) {
		return split('/', substr($_SERVER['PATH_INFO'], 1));
	}
	else {
		return split('/', substr($_SERVER['PATH_INFO'], 1, $pos - 1));
	}
}

function rawman_elem($pic) {
	$elem = array();
	if (preg_match('/(\d{2})(\d{2})(\d{2})_(\d{5})/', $pic, $m)) {
		$elem = array (
			'year'   => $m[1],
			'month'  => $m[2],
			'day'    => $m[3],
			'number' => $m[4]
		);
	}
	return $elem;
}

function rawman_filename($path) {
	// basename without extension
	if ($pos = strrpos($path, '.')) {
		return basename(substr($path, 0, $pos));
	}
	else {
		return basename($path);
	}
}

function rawman_getrawdir($pic) {
	$dirs = rmconf('rawdir');
	$el   = rawman_elem($pic);
	return $dirs[rmconf('elem-dir')] .'20'. $el['year'] .'_'. $el['month'] .'/';
}

function rawman_getpicdir($pic) {
	return sprintf('%s20%02d_%02d', rmconf('picdir'), substr($pic, 0, 2), substr($pic, 2, 2));
}

function rawman_getrawfile($pic) {
	$rwafile  = '';
	$filename = rawman_filename($pic);
	$rawdir   = rawman_getrawdir($pic);
	$ereg     = $filename .'\.';
	$files    = rawman_readdir($rawdir, $ereg, true);
	if (count($files)) {
		$rawfile = array_shift($files);
	}
	return $rawfile;
}

function rawman_date($pic) {
	$el = rawman_elem($pic);
	return sprintf('20%02d-%02d-%02d, #%d', $el['year'], $el['month'], $el['day'], $el['number']);
}

function rawman_number($pic) {
	$el = rawman_elem($pic);
	return sprintf('%d', $el['number']);
}

function rawman_replace($text, $params, $_x = '@@') {
	if (is_array($params)) {
		foreach ($params as $key => $value) {
			if (!empty($key)) {
				$text = preg_replace('|'.$_x.$key.$_x.'|i', $value, $text);
			}
		}
	}
	return $text;
}

function rawman_readtemp($name) {
	return join('', file(RM_TEM .'t_'. $name .'.html'));
}

function rawman_html($name, $params) {
	return rawman_replace(rawman_readtemp($name), $params);
}

function rawman_mkdir($subdirs) {
	$dir = '';
	foreach ($subdirs as $sub) {
		$dir .= $sub .'/';
		if (!is_dir($dir)) {
			mkdir($dir, 0777);
		}
	}
	return $dir;
}

function rawman_readdir($dir, $ereg, $fullpath = false) {
	$items = array();
	if (!is_dir($dir)) return $items;
	$dh = opendir($dir);
	while ($item = readdir($dh)) {
		$fname = $dir . $item;
		if (ereg($ereg, $item)) {
			$items[] = $fullpath ? $fname : $item;
		}
	}
	closedir($dh);
	sort($items);
	return $items;
}

function rawman_showpicture($filename) {
	$arr = array (
		'jpg' => 'image/jpeg',
		'png' => 'image/png'
	);
	$img = pathinfo($filename);
	$ext = $img['extension'];

	if (is_readable($filename)) {
		header("Content-Type: ".(isset($arr[$ext]) ? $arr[$ext] : 'image/jpeg'));
		header("Content-Length: ".filesize($filename));
		readfile($filename);
	}
	else {
		// Incorrect picture
	}
}

function rawman_rating($rate, $change = false) {
	$stars = 6;
	if ($rate < 1 || $rate > 6) {
		$rate = 0;
	}
	$out   = '';
	$begin = 1;
	for ($star = $begin; $star <= $stars; $star++) {
		$class = 'rate' . RetIf($rate >= $star, ' select');
		$out  .= sprintf('<li class="%s"%s></li>',
			$class,
			RetIf($change, ' onclick="rate(this);"')
		);
	}
	return '<ul class="rate">'.$out.'</ul>';
}

function rawman_coloring($color, $change = false) {
	$out   = '';
	foreach(array('red','yellow','green','blue','purple') as $name) {
		$class = $name . RetIf($color == $name, ' select');
		$out .= sprintf('<li class="%s"%s></li>',
			$class,
			RetIf($change, ' onclick="color(this);"')
		);
	}
	return '<ul class="color">'.$out.'</ul>';
}

function rawman_wb($wb, $change = false) {
	$out   = '';
	foreach(array('camera','auto','tungsten','fluorescent','sunny','flash','cloudy','shade') as $name) {
		$class = 'wb '. RetIf($wb == $name, ' select');
		$out .= sprintf('<li class="%s" value="%s" %s></li>',
			$class, $name,
			RetIf($change, ' onclick="wb(this);"')
		);
	}
	return '<ul class="wb">'.$out.'</ul>';
}

function rawman_rotate($rotate, $change = false) {
	$out   = '';
	foreach(array('left','right') as $name) {
		$class = 'rotate '. RetIf($rotate == $name, ' select');
		$out .= sprintf('<li class="%s" value="%s" %s></li>'."<!-- Rotate: %s, Name: %s, Eq: %s -->\n",
			$class, $name,
			RetIf($change, ' onclick="rotate(this);"'),
			$rotate, $name, $rotate == $name ? 'Yes' : 'No'
		);
	}
	return '<ul class="rotate">'.$out.'</ul>';
}

function rawman_readexif($pic) {
	$arr_exif = array(
		'Model' => 'Model',
		'Lens' => 'Lens',
		'DateTimeOriginal' => 'Date',
		'ISO' => 'ISO',
		'WhiteBalance' => 'WB',
		'ExposureProgram' => 'Program',
		'ExposureCompensation' => 'Comp',
		'ShutterSpeed' => 'Shutter',
		'Aperture' => 'Aperture',
		'FocalLength' => 'Focal',
		'FlashMode' => 'Flash',
		'DOF' => 'DOF',
		'FocusDistance' => 'Focus',
	);

	$out  = array();
	$ret  = array();
	$raw  = rawman_getrawfile($pic);
	$exec = bin_exif ." -S -d '%Y-%m-%d %H:%M:%S' -".join(' -', array_keys($arr_exif))." $raw";
	error_log(sprintf("ReadExif:\n%s\nReturn:%s\n", $exec, exec($exec, $out)), 3, '/tmp/rawman.log');
	foreach ($out as $line) {
		list($_h, $_d) = split(': ', $line);
		$ret[] = $arr_exif[trim($_h)] .': '. $_d;
	}
	return $ret;
}

function rawman_convparams($file, $opt = array()) {
	$default = array(
		'dcraw'   => opt_dcraw,
		'cnvpre'  => opt_cnvpre,
		'cnvpost' => opt_cnvpost
	);
	$op = array_merge($default, (array) $opt);

	if (is_file($file)) {
		foreach (file($file) as $line) {
			$line = trim($line);
			if (empty($line) || ereg('^#', $line)) {
				continue;
			}
			if (preg_match('/^(dcraw|cnvpre|cnvpost|wb|rating|coloring)\s?=(.*)$/', $line, $m)) {
				$op[$m[1]] = trim($m[2]);
			}
		}
	}
	else {
		$fh = fopen($file, 'w');
		foreach ($op as $key => $value) {
			fwrite($fh, sprintf("%s = %s\n", trim($key), trim($value)));
		}
		fclose($fh);
	}
	return $op;
}

function rawman_get_dcraw_wb($name) {
	$out = ' -w';
	switch ($name) {
		case 'auto':        $out = ' -a'; break;
		case 'tungsten':    $out = ' -r 328 256 791 256'; break;
		case 'fluorescent': $out = ' -r 503 256 634 256'; break;
		case 'sunny':       $out = ' -r 528 256 409 256'; break;
		case 'flash':       $out = ' -r 625 256 384 256'; break;
		case 'cloudy':      $out = ' -r 578 256 373 256'; break;
		case 'shade':       $out = ' -r 669 256 327 256'; break;
	}
	return $out;
}

function IsEmpty($object) {
	if (!isset($object)) {
		return true;
	}
	if (is_array($object)) {
		return (sizeof($object) == 0);
	}
	else {
		return (empty($object) || is_null($object) || strlen(trim($object)) == 0);
	}
}
function RetIf($condition, $text) {
	return ($condition ? $text : '');
}
function RetDefault($value, $default) {
	return (IsEmpty($value) ? $default : $value);
}

function _fsine($name, $value = null) {
	global $form;
	if ($value == null && is_array($name)) {
		foreach ($name as $key => $val) {
			_fsine($key, $val);
		}
	}
	elseif (IsEmpty($form[$name])) {
		$form[$name] = $value;
	}
}

function _form2text($text) {
	return htmlspecialchars($text, ENT_COMPAT);
}
function _form2pq($name) {
	return trim(FormGet($name));
}
function FormGet($name) {
	return isset($_POST['form'][$name]) ? $_POST['form'][$name] : $_GET['form'][$name];
}
function FldIsEq($name, $value) {
	$v = $GLOBALS['form'][$name];
	return (is_array($v) ? in_array($value, $v) : ($v == $value));
}

function CreateHiddenField($name, $default = '') {
	global $form;
	return sprintf('<input type="hidden" id="form-%s" name="form[%s]" value="%s" />',
		$name, $name, (isset($form[$name]) ? _form2text($form[$name]) : $default)
	);
}
function CreateInputText($name, $size = 40, $default = '', $params = '') {
	global $form;
	return sprintf('<input type="text" size="%d" id="form-%s" name="form[%s]" value="%s" %s />',
		$size, $name, $name, (isset($form[$name]) ? _form2text($form[$name]) : $default), $params
	);
}
function CreateButton($name, $title, $params = '') {
	return sprintf('<input type="button" id="form-%s" name="form[%s]" value="%s" %s />',
		$name, $name, $title, $params
	);
}
function CreateTextArea($name, $cols = 40, $rows = 4, $params = '') {
	global $form;
	return sprintf('<textarea cols="%d" rows="%d" id="form-%s" name="form[%s]" %s>%s</textarea>',
		$cols, $rows, $name, $name, $params, _form2text($form[$name])
	);
}
function CreateSelectField($name, $options = '', $hash = 1, $params = '') {
	global $form;
	$txt  = '';
	$temp = '<option value="%s" %s>%s</option>';
	$fld  = preg_replace('/[\[\]\s]*/', '', $name);
	if (is_array($options) && count($options)) {
		foreach ($options as $key => $label) {
			$val  = ($hash ? $key : $label);
			$txt .= sprintf($temp, $val, RetIf(FldIsEq($fld, $val), 'selected="selected"'), $label);
		}
	}
	return sprintf('<select id="form-%s" name="form[%s]" %s>%s</select>', $name, $name, $params, $txt);
}
function CreateCheckField($name, $options = '', $hash = 1, $maxitem = 0, $params = '') {
	global $form;

	$content = '';
	$temp = '<input type="checkbox" name="form[%s][]" value="%s" %s %s /> %s ';
	if (is_array($options) && count($options)) {
		$numitem = 0;
		foreach ($options as $key => $label) {
			$value    = ($hash ? $key : $label);
			$content .= sprintf($temp, $name, $value, RetIf(FldIsEq($name, $value), 'checked="checked"'), $params, $label);
			$numitem++;
			if ($maxitem && $numitem >= $maxitem) {
				$content .= "<br />\n";
				$numitem  = 0;
			}
		}
	}
	return $content;
}
function CreateCheckOne($name, $desc, $params = '') {
	global $form;
	return sprintf('<input type="checkbox" id="form-%s" name="form[%s]" value="1" %s %s /> %s ',
		$name, $name,
		RetIf(!IsEmpty($form[$name]), 'checked="checked"'),
		RetIf(!IsEmpty($params), $params),
		$desc
	);
}
function CreateRadioField($name, $options = '', $hash = 1, $maxitem = 0) {
	global $form;

	$content = '';
	$temp = '<input type="radio" name="form[%s]" value="%s" %s /> %s';
	if (is_array($options) && count($options)) {
		$numitem = 0;
		foreach ($options as $key => $label) {
			$value    = ($hash ? $key : $label);
			$content .= sprintf($temp, $name, $value, RetIf(FldIsEq($name, $value), 'checked="checked"'), $label);
			$numitem++;
			if ($maxitem && $numitem >= $maxitem) {
				$content .= "<br />\n";
				$numitem  = 0;
			}
		}
	}
	return $content;
}

?>
