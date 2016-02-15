<?php
/**
 * action script for ajax submit of Parts
 */
include_once 'common/base.php';
include_once 'inc/data.inc.php';

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
?>
