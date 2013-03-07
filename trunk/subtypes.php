<?php
include_once "common/base.php";
$pageTitle="Subtypes";
include_once "common/header.php"; 
?>

<div id="main">

	<noscript>This site just doesn't work, period, without JavaScript</noscript>

<?php
	// List subtypes
	include_once 'inc/class.subtypes.inc.php';
    $list = new Subtypes($db);
    
    $editMode = false;
    
    if (isset($_GET['mode'])) {
		switch ($_GET['mode']) {
			case 'add' :
				$list->add($_GET['name']);
				break;
			case 'delete' :
				$list->del($_GET['id']);
				break;
			case 'update' :
				$list->update($_GET['id'], $_GET['name']);
				break;
			case 'edit' :
				$editMode = true;
				break;
		}
	}
	
    $entries = $list->load();
?>
	<table class='bordered'>
	<thead>
		<tr>
			<th>Name</th>
			<th colspan=2>Actions</th>
		</tr>
	</thead>
	<tbody>
<?php
	if ($editMode == false) {
		// if we're in edit mode, we won't display all the parts, just the part form
		// but if we're not in edit mode, then go ahead and display the whole table
		foreach ($entries as $entry) { 
?>
		<tr>
			<td><?php echo $entry['name'] ?></td>
			<td>
				<form action='' method='GET'>
				<input type="hidden" name="mode" value="edit"/>
				<input type='hidden' name='id' value='<?php echo $entry['id']?>'/>
				<input type='submit' value='edit'/>
				</form>
			</td>
			<td>
				<form action='' method='GET'>
				<input type="hidden" name="mode" value="delete"/>
				<input type='hidden' name='id' value='<?php echo $entry['id']?>'/>
				<input type='submit' value='delete'/>
				</form>
			</td>
		</tr>
<?php
	 	}
	} 
?>
		</tbody>
		<tfoot>
		<tr>
			<td>
				<form action="" id="add-new" method="GET"> 
				<input type="hidden" name="mode" value='<?php echo ($editMode)? "update" : "add";?>'>
				<input type="text" name="name" size="20" value='<?php if ($editMode) echo $list->lookup($_GET['id']);?>'>
			</td>
			<td colspan=2 class='centered'>
				<input type="submit" id="add-new-submit" value='<?php echo ($editMode)? "update" : "add";?>' class="button" />
<?php if ($editMode) { ?> 
				<!-- in edit mode we need the id of what we're editing and we also need to be able to cancel -->
				<input type='hidden' name='id' value='<?php echo $_GET['id']; ?>'/>
				<a href="subtypes.php" style="text-decoration: none;" ><input type='button' value="cancel"></a>
<?php } ?>
				</form>
			</td>
		</tr>
		</tfoot>
	</table>

</div>

<?php include_once "common/menu.php"; ?>
<?php include_once "common/footer.php"; ?>
