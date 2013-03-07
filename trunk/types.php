<?php
include_once "common/base.php";
$pageTitle="Types";
include_once "common/header.php"; 
?>

<div id="main">

	<noscript>This site just doesn't work, period, without JavaScript</noscript>

<?php
	// List types
	include_once 'inc/class.types.inc.php';
    $list = new Types($db);
    
    if (isset($_GET['mode'])) {
		if ($_GET['mode'] == "add") {
			$list->add($_GET['name']);
		} else if ($_GET['mode'] == "delete") {
			$list->del($_GET['id']);
		}
	}
	
    $entries = $list->load();
?>
	<form action="" id="add-new" method="GET"> 
	<input type="hidden" name="mode" value="add">
	<table class='bordered'>
		<thead>
		<tr>
			<th>Name</th>
			<th class='centered'>Action</th>
		</tr>
		</thead>
		<tbody>
<?php
	foreach ($entries as $entry) {
		echo "<tr>\n";
		echo "<td>".$entry['name']."</td>";
		echo "<td>";
		echo "<input type='button' value='Edit' />";
		echo "<input type='button' value='Del' />";
		echo "</td>\n";
		echo "</tr>";
	}
?>
		</tbody>
		<tfoot>
		<tr>
			<td><input type="text" name="name" size="20"></td>
			<td class='centered'><input type="submit" id="add-new-submit" value="Add" class="button" /></td>
		</tr>
		</tfoot>
	</table>
	</form>
</div>

<?php include_once "common/menu.php"; ?>
<?php include_once "common/footer.php"; ?>
