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
include_once 'common/base.php';

// Get the API name based on the filename
$me = preg_replace('/\.php$/', '', basename(__FILE__));

// Determine the arguments in the URI
$path = explode("$me/", $_SERVER['REQUEST_URI'] );
$argv = array();
if (isset($path[1])) {
	$argv = explode( ',', $path[1]);
}
$argc = count($argv);

if ($argc > 1) {
	exit(2);
}

// Prep the data access object
$subtype = new Data($db, 'subtypes', 'id', array('name', 'id'));

switch($_SERVER['REQUEST_METHOD']){
	case 'GET':
		if ($argc == 1) {
			// Retrieve one
			echo json_encode($subtype->loadRow('id', $argv[0]));
		} else {
			// Retrieve all
			echo json_encode($subtype->load());
		}
		break;
	case 'POST':
		// Create new
		if (isset($_POST['name'])) {
			$id = $subtype->add($_POST['name']);
			echo json_encode(array('name' => $_POST['name'], 'id' => $id));
		} else {
			echo json_encode(array());
		}
		break;
	case 'DELETE':
		// Delete one
		if ($argc == 1) {
			$id = $argv[0];
			$id = $subtype->del($id);
			echo json_encode($id);
		} else {
			echo json_encode(array());
		}
		break;
	case 'PUT':
		if (argc == 1 && isset($_POST['name'])) {
			$id = $argv[0];
			$data = array('name' => $_POST['name']);
			$id = $subtype->update($id, $data);
			echo json_encode($id);
		} else {
			echo json_encode(array());
		}
		break;
}

?>
