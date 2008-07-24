<?php
/*
** RawManager v. 0.1.0
*/
require_once('./includes/boot.inc.php');

/*
  Photos:
    YYYY_MM/SIZE/yymmdd_nnnnn.jpg
  Icons:
    YYYY_MM/thumb/yymmdd_nnnnn.png
  Stock:
    YYYY_MM/stock/yymmdd_nnnnn.png
*/
	session_start();

	$pars = rawman_pathinfo();
	$what = array_shift($pars);
	$item = array_shift($pars);
	$func = '_rm_page_'.$what;
	if (empty($what) || !function_exists($func)) {
		$func = '_rm_page_main';
	}
	$func($item, $pars);

?>
