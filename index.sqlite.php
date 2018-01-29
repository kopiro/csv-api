<?php

define('START', microtime(1));
header("Content-Type: application/json");

try {
	$db = new PDO('sqlite:data.sqlite3');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->exec("CREATE TABLE IF NOT EXISTS data (id INTEGER PRIMARY KEY, title TEXT, message TEXT, visible INTEGER)");
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST':
		$fh = @fopen($_FILES['csv']['tmp_name'], 'r+');
		if ($fh == false) throw new Exception('Unable to open file'); 
		$stmt = $db->prepare("INSERT INTO data (id, title, message, visible) VALUES (:id, :title, :message, :visible)");
		$skip = true;
		$rows = 0;
		while(($row = fgetcsv($fh, 8192)) !== false) {
			if ($skip === true) {
				$db->query('DELETE FROM data');
				$skip = false;
			} else {
				$rows++;
				$stmt->execute($row);
			}
		}
		$out = ([ 'success' => true, 'rows' => $rows ]);
		break;
		case 'GET':
		$limit = intval(isset($_GET['limit']) ? $_GET['limit'] : 20);
		$page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
		$sqlpage = ($page-1) * $limit;
		if (isset($_GET['id'])) {	
			$stmt = $db->prepare("SELECT * FROM data WHERE id = :id AND visible = 1");
			$stmt->execute([ 'id' => $_GET['id'] ]);
			$out = ['data' => $stmt->fetch(PDO::FETCH_OBJ)];
		} else if (isset($_GET['title'])) {
			$stmt = $db->prepare("SELECT * FROM data WHERE title LIKE :title AND visible = 1 LIMIT $sqlpage, $limit");
			$stmt->execute([ 'title' => ('%'.$_GET['title'].'%') ]);
			$out = ['data' => $stmt->fetchAll(PDO::FETCH_OBJ), 'page' => $page, 'limit' => $limit];
		} else { 
			$stmt = $db->prepare("SELECT * FROM data WHERE visible = 1 LIMIT $sqlpage, $limit");
			$stmt->execute();
			$out = ['data' => $stmt->fetchAll(PDO::FETCH_OBJ), 'page' => $page, 'limit' => $limit];
		}
		break;
	}
} catch (Exception $e) {
	$out = ['error' => [ 'message' => $e->getMessage() ]];
}

header('X-Benchmark: ' . 1000 * (microtime(1) - START));
echo json_encode($out, JSON_NUMERIC_CHECK);