<?php
/**
 * action script for ajax submit of Parts
 */
include_once 'common/base.php';

$id = "";

$subtype = new Data($db, 'subtypes', 'id', array('name', 'id'));

switch($_GET['mode']) {
	case 'list' :
	  echo json_encode($subtype->load());
		break;
		/*
	case 'update' :
		$id = $_GET['id'];
		$data = array('name' => $_GET['name'] );
		$subtype->update($id, $data);
		break;
		*/
	case 'add' :
		$data = array('name' => $_GET['name'] );
		$id = $subtype->add($data);
		$data['id'] = $id;
		echo json_encode($data);
		break;
	case 'delete' :
		$id = $_GET['id'];
		$subtype->del($_GET['id']);
		break;
}

?>
