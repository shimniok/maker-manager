<?php
/**
 * action script for ajax submit of Parts
 */
include_once 'base.php';

// Get the API name based on the current php filename
$me = preg_replace('/\.php$/', '', basename(__FILE__));

// Extract any arguments from the URI
$argv = preg_split('/\//',
	preg_replace( '/^.*\/'.$me.'\/{0,1}/', '',
		$_SERVER['REQUEST_URI']),-1, PREG_SPLIT_NO_EMPTY);
$argc = count($argv);

if ($argc < 0 || $argc > 1) {
	writeLog("arg count wrong (argc=$argc) - ".$_SERVER['REQUEST_URI']);
	exit(2);
}

// Prep the data access object
$db = new Data($db, 'types', 'id', array('name', 'id'));

header('Content-Type: application/json');

header('Content-Type: application/json');

$response = array();

try {

	switch($_SERVER['REQUEST_METHOD']){
		case 'GET':
			if ($argc == 1) {
				// Retrieve one
				$response = $db->loadRow('id', $argv[0]);
				writeLog("$me GET id=".$argv[0]);
			} else {
				// Retrieve all
				$response = $db->load();
				writeLog("$me GET all");
			}
			break;
	}
} catch (Exception $e) {
	writeLog("$me Exception: $e");
}

// Send back whatever the response is
// supposed to be, encoded in JSON.
echo json_encode($response);

/*
$id = "";

// input validation?
switch($_GET['mode']) {
	case 'list' :
		echo json_encode($type->load());
		break;
	case 'update' :
		$id = $_POST['id'];
		$data = array('name' => $_POST['name'] );
		$type->update($id, $data);
		break;
	case 'add' :
		$data = array('name' => $_POST['name'] );
		$id = $type->add($data);
		break;
	case 'delete' :
		$id = $_POST['id'];
		$type->del($_POST['id']);
		break;
}
*/

?>
