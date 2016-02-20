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
		case 'POST':
			// Create new
			if ($json = file_get_contents('php://input')) {
				$params = json_decode($json, true);
				$data = array( 'name' => $params['name'] );
				$data['id'] = $db->add($data);
				$response = $data;
				foreach ($params as $key => $value) {
					writeLog("$me json decode: params[$key]=($value)");
				}
			} else {
				writeLog("$me POST couldn't read php://input");
			}
			break;
		case 'DELETE':
			// Delete one
			if ($argc == 1) {
				if ($db->del($argv[0])) {
					$response = array( 'id' => $argv[0] );
					writeLog("$me DELETE id=$id");
				} else {
					writeLog("$me DELETE fail id=$argv[0]");
				}
			} else {
				writeLog("$me DELETE no id provided");
			}
			break;
	}
} catch (Exception $e) {
	writeLog("$me Exception: $e");
}

// Send back whatever the response is
// supposed to be, encoded in JSON.
echo json_encode($response);

?>
