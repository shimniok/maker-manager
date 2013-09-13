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
$data = array('inventory' => $_POST['inventory'],
			  'ordered' => $_POST['ordered'],
			  'partNo' => $_POST['partNo'],
			  'footprint' => $_POST['footprint'],
			  'value' => $_POST['value'],
			  'voltage' => $_POST['voltage'],
			  'tolerance' => $_POST['tolerance'],
			  'types_id' => $_POST['types_id'],
			  'subtypes_id' => $_POST['subtypes_id']
			 );

switch($_POST['mode']) {
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

// need to echo back JSON version of data
$data['id'] = $id;
echo json_encode($data);
?>
