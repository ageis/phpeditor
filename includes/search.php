<?php

error_reporting(0);

if (!file_exists(".dircache")) {	
	make_cache();
}

if ((time() - filemtime(".dircache")) >= 86400) {
	 make_cache();
}

$term = strtolower(urldecode($_GET["term"]));
$parsed = parse_url($term);

if ( empty($parsed['host']) ) {
	$q = $term;
} else {
	$q = $parsed['host'].$parsed['path'];
}
if ( empty($q) ) return;

$items = explode("\n",rtrim(file_get_contents(".dircache")));

foreach ($items as &$value) {
    $value = str_rot47(($value));
}

$_count = 0;
$_results = array();

foreach ( $items as $key => $value ) {
	if ( strpos($value, $q) > -1 ) {
		array_push($_results, array("id" => $key, "label" => $value, "value" => strip_tags($value)));
		if ( ++$_count > 16 ) break;
	}
}
	echo json_encode($_results);
	
function make_cache() {	
	$cache = shell_exec('find ../../.. -type d -exec ls -d -1 {} \;');
	$paths = explode("\n", $cache);
	$cache2 = array();
	foreach($paths as $path) {
        $path = realpath($path);
        array_push($cache2, str_rot47($path) . "\n");
	}
	file_put_contents('.dircache', $cache2);
	chmod(0700,'.dircache');
}

function str_rot47($str) {
	return strtr($str, '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~', 
	'PQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNO');
}

?>
