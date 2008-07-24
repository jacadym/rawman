<?php

function rawman_createstack($raw, $stack, $opt) {
	$arr = array(
		array(-15,-2,3,7),
		array(-15,-9,-1,5),
		array(1,6,10,15),
		array(-15,-10,-5,-1),
		array(-15,-7,-1,15),
		array(-6,3,8,11),
		array(-14,-7,5,14),
		array(-15,-6,1,13)
	);

	$rnd = $arr[array_rand($arr)];
	shuffle($rnd);
	list ($rand1, $rand2, $rand3, $rand4) = $rnd;

	$opt['cnvpre']  .= ' -thumbnail "120x100>"';
	$opt['cnvpost'] .= ' '.
		'-bordercolor white -border 6 '.
		'-bordercolor grey60 -border 1 '.
		'-background none -background none '.
		'\( -clone 0 -rotate '.$rand1.' \) '.
		'\( -clone 0 -rotate '.$rand2.' \) '.
		'\( -clone 0 -rotate '.$rand3.' \) '.
		'\( -clone 0 -rotate '.$rand4.' \) '.
		'-delete 0 -border 100x80 -gravity center '.
		'-crop 200x160+0+0 +repage -flatten -trim +repage '.
		'-background black \( +clone -shadow 60x4+4+4 \) +swap '.
		'-background none -flatten '.
		'-depth 8 -colors 256 -quality 80';

	exec(sprintf('%s -c %s %s | %s - %s %s %s',
		bin_dcraw, $opt['dcraw'], $raw,
		bin_convert, $opt['cnvpre'], $opt['cnvpost'], $stack
	));
}

function rawman_createthumb($raw, $thumb, $opt) {
	$annotate = rawman_replace(THUMB_ANN, array(
		'year'   => isset($opt['year']) ? $opt['year'] : date('Y'),
		'number' => sprintf('%04d', $opt['number'])
	));
	/*
	** Others
	*/
	$opt['cnvpre']  .= ' -size 200x200 -thumbnail "110x90>"';
	$opt['cnvpost'] .= ' -depth 8 -colors 256 -quality 80';

	exec(sprintf('%s -c %s %s | %s - %s %s %s',
		bin_dcraw, $opt['dcraw'], $raw,
		bin_convert, $opt['cnvpre'], $opt['cnvpost'], $thumb
	));
}

function rawman_createthumb_prev($raw, $thumb, $opt) {
	$annotate = rawman_replace(THUMB_ANN, array(
		'year'   => isset($opt['year']) ? $opt['year'] : date('Y'),
		'number' => sprintf('%04d', $opt['number'])
	));
	/*
	** Others
	*/
	$opt['cnvpre']  .= ' -thumbnail "120x>"';
	$opt['cnvpost'] .= ' -extent "120x80" -background white -gravity center -extent "128x100+0+4"';

	if (!empty($annotate)) {
		$opt['cnvpost'] .=
		' -pointsize 11 -gravity south -annotate 0 "'.$annotate.'"';
	}
	$opt['cnvpost'] .=
		' -bordercolor grey60 -border 1'.
		' -background none'.
		' -background black \( +clone -shadow 60x4+4+4 \) +swap'.
		' -background none -flatten'.
		' -depth 8 -colors 256 -quality 80';

	exec(sprintf('%s -c %s %s | %s - %s %s %s',
		bin_dcraw, $opt['dcraw'], $raw,
		bin_convert, $opt['cnvpre'], $opt['cnvpost'], $thumb
	));
}

function rawman_createimage($raw, $image, $opt) {
	$annotate = rawman_replace(IMAGE_ANN, array(
		'year'   => isset($opt['year']) ? $opt['year'] : date('Y'),
		'number' => sprintf('%04d', $opt['number'])
	));
	/*
	** Others
	*/
	$opt['cnvpre']  .= ' -geometry '. IMAGE_SIZE;
	if (!empty($annotate)) {
		$opt['cnvpost'] .=
		' -pointsize 11 -gravity southeast'.
		' -stroke "#000c" -strokewidth 2 -annotate 0 "'.$annotate.'"'.
		' -stroke  none   -fill white    -annotate 0 "'.$annotate.'"';
	}
	$opt['cnvpost'] .= ' -quality 85';

	rawman_createjpg($raw, $image, $opt);
}

function rawman_createjpg($raw, $image, $opt) {
	error_log(sprintf("%s -c %s %s | %s - %s %s %s\n",
		bin_dcraw, $opt['dcraw'], $raw,
		bin_convert, $opt['cnvpre'], $opt['cnvpost'], $image
	),
		3,
		'/tmp/rawman.log'
	);
	exec(sprintf('%s -c %s %s | %s - %s %s %s',
		bin_dcraw, $opt['dcraw'], $raw,
		bin_convert, $opt['cnvpre'], $opt['cnvpost'], $image
	));
}

?>
