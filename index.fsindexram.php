<?php

/* diskutil erasevolume HFS+ 'RAM' `hdiutil attach -nomount ram://1048576` */

define('START', microtime(1));
define('DIR', '/Volumes/RAM');

try {
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST':
		$fh = @fopen($_FILES['csv']['tmp_name'], 'r+');
		if ($fh == false) throw new Exception('Unable to open file'); 
		$i = 0;
		while (($row = fgetcsv($fh, 8192)) !== false) {
			file_put_contents(DIR . '/' . ($i++) . '.json', json_encode($row));
		}
		$out = '{ "success": true }';
		break;
		case 'GET':
		if (isset($_GET['id'])) {
			$fh = fopen(DIR . '/' . intval($_GET['id']) . '.json', 'r');
			$out = fread($fh, 512);
		}
		break;
	}
} catch (Exception $e) {
	$out = '{ "error": "' . $e->getMessage() . '" }';
}

header('X-Benchmark: ' . 1000 * (microtime(1) - START));
echo $out;