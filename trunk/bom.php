<?php
include_once "common/base.php";
$pageTitle="BOM";
include_once "common/header.php"; 
?>

<div id="main">

	<noscript>This site just doesn't work, period, without JavaScript</noscript>
<?php
	include_once 'inc/class.products.inc.php';
	$prod = new Products($db);
	$prod->load();
	include_once 'inc/class.bom.inc.php';
    $bom = new BOM($db);
	if ( isset($_GET['mode']) ) {
	    switch($_GET['mode']) {
			case 'add' :
				$bom->add($_GET['products_id'], $_GET['parts_id'], $_GET['qty']);
				break;
			case 'edit' :
				$bom->update($_GET['products_id'], $_GET['parts_id'], $_GET['qty']);
				break;
			case 'delete' :
				$bom->del($_GET['products_id'], $_GET['parts_id']);
				break;				
		}
	}
    $bomentries = $bom->load($_GET['products_id']);

	// List products
	include_once 'inc/class.parts.inc.php';
	$part = new Parts($db);
	$entries = $part->load();

	include_once 'inc/class.types.inc.php';
	$type = new Types($db);
	$type->load();
	
	include_once 'inc/class.subtypes.inc.php';
	$subtype = new Subtypes($db);
	$subtype->load();

	// Print out name of the product
?>
	<h3 id='bom-header'><?php echo $prod->lookup($_GET['products_id']); ?></h3>
	<table class='bordered'>
		<tr>
			<th class='centered'>Qty</th>
			<th>Part No.</th>
			<th>Footprint</th>
			<th>Value</th>
			<th>Voltage</th>
			<th>Tolerance</th>
			<th>Type</th>
			<th>SubType</th>
			<th class='centered'>Action</th>
		</tr>
<?php foreach ($bomentries as $entry) {
		$p = $part->lookup($entry['parts_id']); ?>
			<tr>
			<td>
				<form action='' method='GET'>
				<input type='hidden' name='mode' value='edit'/>
				<input type='hidden' name='parts_id' value='<?php echo $entry['parts_id'] ?>'/>
				<input type='hidden' name='products_id' value='<?php echo $entry['products_id'] ?>'/>
				<input type='text' name='qty' size='2' value='<?php echo $entry['qty'] ?>'/>
				<input type='submit' value='update'/>
				<!--
				<img class='edit' alt='update' src='images/transparent.png'/>
				<img class='save' alt='update' src='images/transparent.png'/>
				<img class='cancel' alt='update' src='images/transparent.png'/>
				-->
				</form>
			</td>
			<td><?php echo $p['partNo']; ?></td>
			<td><?php echo $p['footprint']; ?></td>
			<td><?php echo $p['value']; ?></td>
			<td><?php echo $p['voltage']; ?></td>
			<td><?php echo $p['tolerance']; ?></td>
			<td><?php echo $type->lookup($p['types_id']); ?></td>
			<td><?php echo $subtype->lookup($p['subtypes_id']); ?></td>
			<td>
				<form action='' method='GET'>
				<input type='hidden' name='mode' value='delete'/>
				<input type='hidden' name='parts_id' value='<?php echo $entry['parts_id'] ?>'/>
				<input type='hidden' name='products_id' value='<?php echo $entry['products_id'] ?>'/>
				<input type='submit' value='delete'/>
				</form>
			</td>
		</tr>
<?php } ?>
		</tbody>
		<tfoot>
		<tr>
			<form action="" id="add-new" method="GET"> 
			<input type="hidden" name="mode" value="add" />
			<input type="hidden" name="products_id" value="<?php echo $_GET['products_id'] ?>" />
			<td><input type="text" name="qty" size=3 value='1' /></td>
			<td colspan='7' class='centered'><select name='parts_id'>
<?php 

	/*
	function sortByTypes($a, $b) {
		echo $a['types_id']." =?= ".$b['types_id'];
	    if ($a['types_id'] == $b['types_id']) {
			return 0;
    	}
    	return ($a['types_id'] < $b['types_id']) ? -1 : 1;
	}

	// Try to group types and subtypes
	usort($entries, sortByTypes);
	*/

	foreach ($entries as $entry) {
		// Convert to formatter routine
		$name = $type->lookup($entry['types_id']) . " " .
				$subtype->lookup($entry['subtypes_id']) . " " .
				$entry['value'] . " " .
				$entry['voltage'] . " " .
				$entry['footprint'] . " " .
				$entry['tolerance'] . " " .
				$entry['partNo'];
		echo "<option value='".$entry['id']."'>".$name."</option>";
	}
?>	
				</select>
			</td>
			<td class='centered'><input type="submit" id="add-new-submit" value="Add" class="button" /></td>
			</form>
		</tr>
		</tfoot>
	</table>

</div>

<?php include_once "common/menu.php"; ?>

<?php include_once "common/footer.php"; ?>
