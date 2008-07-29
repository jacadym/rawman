<?php

rmconf('rawdir', array(
	'nef1' => '/opt/raw/NikonD70/',
	'net2' => '/opt/raw/NikonD300/'
));
rmconf('picdir', '/opt/pic/');

define('RM_WEB', 'http://'.$_SERVER['HTTP_HOST'].'/rawman');
define('RM_TEM', dirname(dirname(__FILE__)).'/skins/');

// Binary programs
define('bin_dcraw',   '/usr/bin/dcraw');
define('bin_convert', '/usr/bin/convert');
define('bin_exif',    '/usr/bin/exiftool');

// Annotate text
define('IMAGE_ANN', " # @@number@@  -  @@year@@  ©   <username> ");
define('THUMB_ANN', " # @@number@@ ");
define('IMAGE_SIZE', '1024x800');

define('opt_dcraw', '-w -h -b 1.33');
define('opt_cnvpre', '-shave 4x4 -gamma 1.15');
define('opt_cnvpost', '-unsharp 3x3+0.3+0');

?>
