<?php
include_once "common/base.php";
$pageTitle="Products";
include_once "common/header.php"; 
?>

<div id="main">

	<noscript>This site just doesn't work, period, without JavaScript</noscript>

<?php
	// List products
	include_once 'inc/class.products.inc.php';
    $list = new Products($db);
    
    include_once 'inc/class.bom.inc.php';
    $bom = new BOM($db);
    
    if (isset($_GET['mode'])) {
		switch($_GET['mode']) {
			case 'add' :		
				$list->add($_GET['name']);
				break;
			case 'delete' :
				$list->del($_GET['id']);
				break;
		}
	}
	
    $entries = $list->load();
?>
	<table class='bordered'>
		<thead>
		<tr>
			<th>Name</th>
			<th>Parts</th>
			<th>Unique</th>
			<th colspan='2' class='centered'>Action</th>
        </tr>
        </thead>
        <tbody>
<?php 
	foreach ($entries as $entry) { 
		$bomentry = $bom->load($entry['id']);
?>
		<tr>
			<td><?php echo $entry['name'] ?></td>
			<td class='centered'><?php echo $bom->partCount(); ?></td>
			<td class='centered'><?php echo $bom->uniqueCount();?></td>
			<td>
				<!-- bom -->
				<form action='bom.php' method='GET'>
				<input type='hidden' name='mode' value='bom'/>
				<input type='hidden' name='products_id' value="<?php echo $entry['id'] ?>"/>
				<input type='submit' value='bom' />
				</form>
			</td>
			<td>
				<!-- delete -->
				<form action='' method='GET'>
				<input type='hidden' name='mode' value='delete'/>
				<input type='hidden' name='id' value="<?php echo $entry['id'] ?>"/>
				<input type='submit' value='delete' />
				</form>
			</td>
		</tr>
<?php } ?>
		</tbody>
		<tfoot>
		<tr>
			<form action="" id="add-new" method="GET"> 
			<input type="hidden" name="mode" value="add">
			<td><input type="text" name="name" size="20"></td>
			<td></td>
			<td></td>
			<td colspan='2' class='centered'><input type="submit" id="add-new-submit" value="Add" class="button" /></td>
			</form>
		</tr>
		</tfoot>
	</table>
</div>

<?php include_once "common/menu.php"; ?>
<?php include_once "common/footer.php"; ?>
