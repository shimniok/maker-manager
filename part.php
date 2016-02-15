<?php
/**
 * action script for ajax submit of Parts
 */
include_once 'common/base.php';

$part = new Data($db, 'parts', 'id',
				 array('id', 'inventory', 'ordered', 'partNo', 'footprint',
					   'value', 'voltage', 'tolerance', 'types_id', 'subtypes_id'));

// input validation?
$id = "";
if (isset($_POST['id'])) {
	$id = $_POST['id'];
}

switch($_GET['mode']) {
	case 'list' :
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// headers to tell that result is JSON
		header('Content-type: application/json');

		echo json_encode($part->load());
		break;
	case 'update' :
		$part->update($id, $data);
		break;
	case 'add' :
		$id = $part->add($data);
		break;
	case 'received' :
		$row = $part->loadRow('id', $id);
		$data['inventory'] = $row['inventory'] + $row['ordered'];
		$data['ordered'] = $row['ordered'] = 0;
		$part->update($id, $data);
		break;
	case 'delete' :
		$part->del($id);
		break;
}

?>
