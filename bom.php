<script type='text/javascript'>actionUrl="bom.action.php";</script>
<?php
/**
 * bom.php - display list of products and associated parts, editable
 * 
 * @author Michael Shimniok
 */
include_once "common/base.php";
$pageTitle="BOM";
include_once "common/header.php"; 
include_once 'inc/data.inc.php';

$prodList = $prod->load();
$bomList = $bom->load();
$partList = $part->load();
$countList = $partcount->load();
$typeList = $type->load();
$subTypeList = $subtype->load();

$data = array(
	'main' => null, // will be filled in with product_id-specific partlist
	'bom' => $bomList,
	'parts' => $partList,
	'types' => $typeList,
	'subtypes' => $subTypeList,
	'counts' => $countList	
);

$metadata = array(
	'available' => array( 'title' => 'Available', 'edit' => false, 'table' => 'counts', 'key' => 'id', 'col' => 'available', 'negative' => true, 'low' => 10 ),
	'ordered' => array( 'title' => 'Ordered', 'edit' => false, 'table' => 'parts', 'key' => 'id', 'col' => 'ordered' ),
	'inventory' => array( 'title' => 'Inventory', 'edit' => false, 'table' => 'parts', 'key' => 'id', 'col' => 'inventory' ),
	'partNo' => array( 'title' => 'Part No.', 'edit' => true ), 
	'footprint' => array( 'title' => 'Footprint', 'edit' => true ),  
	'value' => array( 'title' => 'Value', 'edit' => true ),  
	'voltage' => array( 'title' => 'Voltage', 'edit' => true ),  
	'tolerance' => array( 'title' => 'Tolerance', 'edit' => true ),
	'types_id' => array( 'title' => 'Type', 'edit' => true, 'table' => 'types', 'key' => 'types_id', 'col' => 'name' ),
	'subtypes_id' => array( 'title' => 'Subtype', 'edit' => true, 'table' => 'subtypes', 'key' => 'subtypes_id', 'col' => 'name' )
);

?>


<!-- Display a select list of all the products. Display the BOM for the selected product. -->
<!-- I realize this is ugly, customized stuff; needs to be included in table.php -->
<body>
    <script src="js/jquery.inline.edit.js" type="text/javascript"></script>
    <div id="page-wrap">
        <div id="header">
            <h1><a href="/mmrp">MakerMRP</a></h1>
            <h2><?php echo $pageTitle ?></h2>
        </div>

<!-- Part selector popup box -->
<div id="popup_box" class='bordered'><?php
	$pdata = array(
		'main' => $partList, // main table
		'types' => $typeList, 
		'subtypes' => $subTypeList,
		'counts' => $countList
	);

	$pmeta = array(
		'partNo' => array( 'title' => 'Part No.', 'edit' => true ), 
		'footprint' => array( 'title' => 'Footprint', 'edit' => true ),  
		'value' => array( 'title' => 'Value', 'edit' => true ),  
		'voltage' => array( 'title' => 'Voltage', 'edit' => true ),  
		'tolerance' => array( 'title' => 'Tolerance', 'edit' => true ),
		'types_id' => array( 'title' => 'Type', 'edit' => true, 'table' => 'types', 'key' => 'types_id', 'col' => 'name' ),
		'subtypes_id' => array( 'title' => 'Subtype', 'edit' => true, 'table' => 'subtypes', 'key' => 'subtypes_id', 'col' => 'name' )
		//'action' => array( 'title' => 'Action', 'button' => 'select')
	);?>
    <h1>Part Selector</h1><?php
	renderTable($pmeta, $pdata, false, 15, 'add'); ?>
    <a id="popupBoxClose">Close</a>
</div>

<div id="main">
	<form id="bom-select">
		<select name='bom-select'>
<?php	foreach ( $prodList as $pr_id => $prod ) { 
			echo "<option value='".$pr_id."'>".$prod['name']."</option>";
		}?>
		</select>
	</form>

	<ul><?php 
	foreach ( $prodList as $pr_id => $prod ) { ?>
	<li class="bom" id="<?php echo $pr_id ?>"><span>
		<?php echo $prod['name']." - Needed:".$prod['needed']; ?></span>
		<table class='editable bordered' id='<?php echo $pr_id; ?>'>
		<thead>
			<tr><th><ul> 
			<li class='qty'>Qty</li><?php
			foreach ( $metadata as $col => $meta ) {
				echo "<li class='".$col."'>".$meta['title']."</li>";			
			} ?>
			<li class='action'>Action</li>			
			</ul></th></tr>
		</thead>
		<tbody>
			<?php
			// Loop thru list of bom items looking for match to current product id
			foreach ( $data['bom'] as $bom_id => $bom) {
				// if current product id matches bom array id entry
				if ( $pr_id == $bom['products_id'] ) {
					// list part details
					$pid = $bom['parts_id'];
					$part = $data['parts'][$pid];?>
					<tr><td><form id='<?php echo $row["id"];?>'><ul>
						<li class='qty'>
						<span class='text'><?php echo $bom['qty']; ?></span>
						<span class='edit'>
							<input type='text' class='qty' value='<?php echo $bom['qty']; ?>' name='qty'/>
						</span>
						</li><?php
					foreach ($metadata as $col => $meta) {
						// If a table lookup is specified, do the lookup,
						// otherwise just print out the row value directly
						$myval = '';
						if (isset($meta['table'])) {
							$table = $data[$meta['table']]; // table to be looked up
							$key = $part[$meta['key']]; // fkey value in main table
							$fcol = $meta['col']; // foreign column name
							$myval = $table[$key][$fcol];
						} else {
							$myval = $part[$col];
						}
						echo "<li class='".$col."' value='".$pid."'>";

						// Higlights low inventory numbers
						if ($myval < 0 && isset($meta['negative'])) { ?>
							<span class='alert'>
<?php					} elseif (isset($meta['low']) && $myval <= $meta['low']) { ?>
							<span class='warning'>
<?php					} else { ?>
							<span class='nothing'>
<?php					}
						echo $myval;
?>						</span>
						</li>
<?php				}
?>					<li class='action'>
						<span class='button'>
							<input type='button' value='delete' class='delete'/>
							<input type='hidden' value='<?php echo $bom_id; ?>' name='id' />
							<input type='hidden' value='<?php echo $pr_id; ?>' name='products_id' />
							<input type='hidden' value='<?php echo $pid; ?>' name='parts_id' />
						</span>
						<span class='edit'>
							<input type='submit' value='submit' class='save'/>
							<input type='button' value='cancel' class='cancel'/>
						</span>
					</li>
					</ul></form></td></tr><?php
				}
			}?>
			<!-- Add a new part -->
			<tr id='last'><td><form class='bom-add'><ul>
				<li class='qty'>
					<input type='text' class='qty' name='qty' value='1'/>
					<input type='hidden' name='products_id' value='<?php echo $prod['id']; ?>'/>
				</li>
				<!--<li class='bom-select'><?php echo $partSelector; ?></li>-->
				<li class='action'><input id='<?php echo $prod['id']; ?>' type='submit' value='bom-add' class='bom-add'/></li>
			</ul></form></td></tr>
		</tbody>
		</table>
		<a href="#">top</a>
		</span></li>
		<?php	
	} ?>
	</ul>
</div>

<?php

//renderTable2($metadata, $data);

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
