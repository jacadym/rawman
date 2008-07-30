<?php
/*
** RawManager v. 0.1.0
*/
include('./includes/boot.inc.php');

/*
  Photos:
    YYYY_MM/SIZE/yymmdd_nnnnn.jpg
  Icons:
    YYYY_MM/thumb/yymmdd_nnnnn.png
  Stock:
    YYYY_MM/stock/yymmdd_nnnnn.png

  Po wejściu na stronę główną następuje wyświetlenie listy dostępnych katalogów wraz z ikonami stosu
  dla każdego miesiąca. Brany jest pod uwagę pierwsza ikona, która znajduje się w odpowiednim katalogu.
  Jeżeli nie ma ikony to przeszukiwany jest katalog w poszukiwaniu dowolnego rawpliku i utworzenie
  z niego ikon stosu.
  
  Po wybraniu dowolnego katalogu następuje wyświetlenie jego zawartości.

*/

	session_start();

	// what/udir/item
	$pars = rawman_pathinfo();
	$what = array_shift($pars);
	$udir = array_shift($pars);
	$item = array_shift($pars);
	$func = '_rm_page_'.$what;
	if (empty($what) || !function_exists($func)) {
		$func = '_rm_page_main';
	}
	rmconf('elem-dir', $udir);
	rmconf('elem-id', $item);
	$func($item, $pars);

?>
