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

/** Move all this shit into base.php */

/** End move shit */

if ($argc < 0 || $argc > 1) {
	writeLog("arg count wrong (argc=$argc) - ".$_SERVER['REQUEST_URI']);
	exit(2);
}

header('Content-Type: application/json');

// Prep the data access object
$subtype = new Data($db, 'subtypes', 'id', array('name', 'id'));

$response = array();

try {

	switch($_SERVER['REQUEST_METHOD']){
		case 'GET':
			if ($argc == 1) {
				// Retrieve one
				$response = $subtype->loadRow('id', $argv[0]);
				writeLog("GET id=".$argv[0]);
			} else {
				// Retrieve all
				$response = $subtype->load();
				writeLog("GET all");
			}
			break;
		case 'POST':
			// Create new
			if ($json = file_get_contents('php://input')) {
				$params = json_decode($json, true);
				$data = array( 'name' => $params['name'] );
				$data['id'] = $subtype->add($data);
				$response = $data;
				foreach ($params as $key => $value) {
					writeLog("json decode: params[$key]=($value)");
				}
			} else {
				writeLog("POST couldn't read php://input");
			}
			break;
		case 'DELETE':
			// Delete one
			if ($argc == 1) {
				$id = $subtype->del($argv[0]);
				$response = array( 'id' => $id );
				echo json_encode($id);
				writeLog("DELETE id=$id");
			} else {
				writeLog("DELETE no id provided");
			}
			break;
		case 'PUT':
			if (argc == 1 && isset($_POST['name'])) {
				$id = $argv[0];
				$data = array('name' => $_POST['name']);
				$id = $subtype->update($id, $data);
				$response = array( 'id' => $id );
				writeLog("PUT id=$id data=(".implode(' ', $_POST).")");
			} else {
				writeLog("PUT no data provided");
			}
			break;
	}
} catch (Exception $e) {
	writeLog("Exception: $e");
}

// Send back whatever the response is
// supposed to be, encoded in JSON.
echo json_encode($response);

?>
