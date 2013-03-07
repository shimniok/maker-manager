<?php 
include_once 'common/base.php';
$pageTitle='Parts';
include_once 'common/header.php'; 

	// List parts
	include_once 'inc/class.parts.inc.php';
	include_once 'inc/class.types.inc.php';
	include_once 'inc/class.subtypes.inc.php';
	$part = new Parts($db);
	$type = new Types($db);
	$subtype = new Subtypes($db);

	/**
	 * The state machine here is a bit complex
	 * By default, we show all parts
	 * If the user clicks 'delete' button on a particular part, then the page calls itself with mode=delete
	 *   passing in the id of the particular part chosen for deletion. The part is deleted and we display the table as normal
	 * If the user clicks 'add' then the page calls itself with mode=add, passing along the various fields entered
	 *   into the add/update form
	 * If the user clicks 'edit' then the page calls itself with mode=edit, passing the id of the part to edit
	 *   and the page skips displaying the full table, only displaying the add/update form with appropriate changes
	 * 	 from here if the user clicks 'update' then the page calls itself again, passing along the parameters in the form
	 *   else if the user clicks 'cancel' then the page calls itself with no mode
	 * If the user clicks 'update' on the inventory bit, the page calls itself with mode=inventory and 
	 *   passes the id and the new inventory value and then upon reload, the part's inventory is updated and the display
	 *   occurs as normal.
	 */

	$editMode = false;
	if (isset($_GET['mode'])) {
		switch ($_GET['mode']) {
			case 'inventory' :
				$part->updateInventory( $_GET['id'],
									    $_GET['inventory'] );
				break;
			case 'add' :
				$part->add( $_GET['inventory'],
							$_GET['partNo'],
							$_GET['footprint'],
							$_GET['value'],
							$_GET['voltage'],
							$_GET['tolerance'],
							$_GET['types_id'],
							$_GET['subtypes_id'] );
				break;
			case 'edit' :
				$editMode = true;
				// set default values for the form
				break;
			case 'update' :
				$part->update( $_GET['id'],
							   $_GET['inventory'],
							   $_GET['partNo'],
							   $_GET['footprint'],
							   $_GET['value'],
							   $_GET['voltage'],
							   $_GET['tolerance'],
							   $_GET['types_id'],
							   $_GET['subtypes_id'] );
				break;
			case 'delete' :
				$part->del( $_GET['id'] );
				break;
		}
	}

	$entries = $part->load();
	$typeArr = $type->load();
	$subtypeArr = $subtype->load();
?>
	<div id="main">

	<noscript>This site just doesn't work, period, without JavaScript</noscript>
	<table class='bordered'>
	<thead>
		<tr>
			<th>Qty</th>
			<th>Part No.</th>
			<th>Footprint</th>
			<th>Value</th>
			<th>Voltage</th>
			<th>Tolerance</th>
			<th>Type</th>
			<th>SubType</th>
			<th colspan=2>Actions</th>
		</tr>
	</thead>
	<tbody>
<?php 
	if ($editMode) {
		// Setup the entry array so we can populate defaults into the form
		$entry = $part->lookup($_GET['id']);
	} else {
		// if we're in edit mode, we won't display all the parts, just the part form
		// but if we're not in edit mode, then go ahead and display the whole table

	foreach ($entries as $entry) { 
?>
		<tr>
			<td><!-- separate form to individually, quickly update inventories -->
				<form action='' method='GET'>
				<input type='hidden' name='mode' value='inventory'/>
				<input type='hidden' name='id' value='<?php echo $entry['id']; ?>'/>
				<input type='text' size='3' name='inventory' value='<?php echo $entry['inventory']; ?>'/>
				<input type='submit' value='update'/>
				</form>
			</td>
			<td><?php echo $entry['partNo']; ?></td>
			<td><?php echo $entry['footprint']; ?></td>
			<td><?php echo $entry['value']; ?></td>
			<td><?php echo $entry['voltage']; ?></td>
			<td><?php echo $entry['tolerance']; ?></td>
			<td><?php echo $type->lookup($entry['types_id']); ?></td>
			<td><?php echo $subtype->lookup($entry['subtypes_id']); ?></td>
			<td>
				<form action='' method='GET'>
				<input type='hidden' name='id' value='<?php echo $entry["id"]; ?>'/>
				<input type='hidden' name='mode' value='edit'/>
				<input type='submit' value='edit'/>
				</form>
			</td>
			<td>
				<form action='' method='GET'>
				<input type='hidden' name='id' value='<?php echo $entry["id"]; ?>'/>
				<input type='hidden' name='mode' value='delete'/>
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
	<!-- add/update a part -->
	<tr>
		<form action="" method='GET'>
		<td><input type="text" name="inventory" size="3" value="<? if ($editMode) echo $entry['inventory'];?>"></td>
		<td><input type="text" name="partNo" size="25" value="<? if ($editMode) echo $entry['partNo'];?>"></td>
		<td><input type="text" name="footprint" size="10" value="<? if ($editMode) echo $entry['footprint'];?>"></td>
		<td><input type="text" name="value" size="8" value="<? if ($editMode) echo $entry['value'];?>"></td>
		<td><input type="text" name="voltage" size="4" value="<? if ($editMode) echo $entry['voltage'];?>"></td>
		<td><input type="text" name="tolerance" size="4" value="<? if ($editMode) echo $entry['tolerance'];?>"></td>
		<td><select name="types_id">
<?php
	// list all the types as options
	foreach ($typeArr as $t) {
		echo "<option value='".$t['id']."'";
		// if in edit mode, select the appropriate option
    	if ($editMode && ($t['id'] == $entry['types_id']))
			echo " selected";
		echo ">".$t['name']."</option>";
	}
	echo "<option>add new...</option>";
?>
		</select></td>
		<td><select name="subtypes_id">
<?php
	// list all the subtypes as options
	foreach ($subtypeArr as $s) {
		echo "<option value='".$s['id']."'";
		// if in edit mode, select the appropriate option
    	if ($editMode && ($s['id'] == $entry['subtypes_id']))
			echo " selected";
		echo ">".$s['name']."</option>";
	}
	echo "<option>add new...</option>";
?>
		</select></td>
		<td colspan='2' class='centered'>
		<!-- if we're in edit mode, set mode to update, else set to add -->
		<input type="hidden" value="<?php echo ($editMode)?'update':'add';?>" name="mode">
		<!-- if we're in edit mode, set the submit button value to update, else set to add -->
		<input type="submit" value="<?php echo ($editMode)?'update':'add';?>">
<?php if ($editMode) { ?>
		<!-- in edit mode we need the id of what we're editing and we also need to be able to cancel -->
		<input type='hidden' name='id' value='<?php echo $entry["id"]; ?>'/>
		<a href="parts.php" style="text-decoration: none;" ><input type='button' value="cancel"></a>
<?php } ?>
		</form></td>
	</tr>
	</tfoot>
	</table>

</div>

<?php include_once "common/menu.php"; ?>
<?php include_once "common/footer.php"; ?>
