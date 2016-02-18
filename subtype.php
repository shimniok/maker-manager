<?php
/**
 * REST interface
 *
 * Expects to be called with the following pattern:
 * GET BASE_URL/thingy       - Retrieves a list of thingys
 * GET BASE_URL/thingy/12    - Retrieves a specific thingy (#12)
 * POST BASE_URL/thingy      - Creates a new thingy
 * PUT BASE_URL/thingy/12    - Updates thingy #12
 * DELETE BASE_URL/thingy/12 - Deletes thingy #12
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
$db = new Data($db, 'subtypes', 'id', array('name', 'id'));

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
					writeLog("$me DELETE id=$argv[0]");
				} else {
					writeLog("$me DELETE fail id=$argv[0]");
				}
			} else {
				writeLog("$me DELETE no id provided");
			}
			break;
		case 'PUT':
			if (argc == 1 && isset($_POST['name'])) {
				$id = $argv[0];
				$data = array('name' => $_POST['name']);
				$id = $db->update($id, $data);
				$response = array( 'id' => $id );
				writeLog("$me PUT id=$id data=(".implode(' ', $_POST).")");
			} else {
				writeLog("$me PUT no data provided");
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
