<?php
/**
 * action script for ajax submit of Products
 */
include_once 'common/base.php';

$prod = new Data($db, 'products', 'id', array('id', 'name', 'inventory', 'needed', 'sold'));

// input validation?
$id = '';
if (isset($_POST['id'])) {
	$id = $_POST['id'];
}
	
$data = array('name' => $_POST['name'],
			  'inventory' => $_POST['inventory'],
			  'needed' => $_POST['needed'],
			  'sold' => $_POST['sold']
			 );

switch($_POST['mode']) {
	case 'update' :
		$prod->update($id, $data);
		break;
	case 'add' :
		$prod->add($data);
		break;
	case 'needed' :
		$row = $prod->loadRow('id', $id);
		$data['needed'] = ++$row['needed'];
		$prod->update($id, $data);
		break;
	case 'built' :
		$row = $prod->loadRow('id', $id);
		$data['inventory'] = $row['inventory'] + 1;
		$data['needed'] = $row['needed'] - 1;
		$prod->update($id, $data);
		break;
	case 'sold' :
		$row = $prod->loadRow('id', $id);
		$data['sold'] = $row['sold'] + 1;
		$data['inventory'] = $row['inventory'] - 1;
		$prod->update($id, $data);
		break;
	case 'delete' :
		$prod->del($id);
		break;
}

// need to echo back JSON version of data
$data['id'] = $id;
echo json_encode($data);
?>
