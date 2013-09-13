<?php
/**
 * action script for ajax submit of BOM
 */
include_once 'common/base.php';
include_once 'inc/data.inc.php';

// input validation?
$id = "";
if (isset($_POST['id'])) {
	$id = $_POST['id'];
}
$data = array(
	'qty' => $_POST['qty'],
	'parts_id' => $_POST['parts_id'],
	'products_id' => $_POST['products_id']
);

switch($_POST['mode']) {
	case 'update' :
		$bom->update($id, $data);
		break;
	case 'add' :
		$id = $bom->add($data);
		break;
	case 'delete' :
		$bom->del($id);
		break;
}

// need to echo back JSON version of data
$data['id'] = $id;
echo json_encode($data);
?>
