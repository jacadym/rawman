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
	if (is_array($_SESSION['bookmark'])) {
		foreach ($_SESSION['bookmark'] as $img => $val) {
			$class    = 'book';
			$content .= sprintf('<img id="%s" src="%s/index.php/thumb/%s.png" onclick="selImg(this.id)" class="%s" title="%s" />',
				$img, RM_WEB, $img, $class, rawman_date($img)
			);
		}
	}
	return $content;
}

function _rm_page_bookmark() {
	echo rawman_html('day', array(
		'skins'  => RM_WEB .'/skins',
		'imgdir' => RM_WEB.'/index.php/image',
		'thumbs' => rawman_listbookmark()
	));
}

?>
