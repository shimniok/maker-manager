<script type='text/javascript'>actionUrl='product.action.php';</script>
<?php

/**
 * parts.php - display list of parts, editable
 * 
 * @author Michael Shimniok
 */
include_once 'common/base.php';
$pageTitle='Products';
include_once 'common/header.php';
include_once 'inc/data.inc.php';

$data = array( 'main' => $prod->load() );
$metadata = array( 'name' => array('title' => 'Name', 'edit' => true),
				   //'parts' => array('title' => 'Parts', 'edit' => false, 'table' = $count),
				   //'unique' => array('title' => 'Unique', 'edit' => false, 'table' = $unique),
				   'needed' => array('title' => 'Needed', 'edit' => true, 'button' => 'needed'),
				   'inventory' => array('title' => 'Inventory', 'edit' => true, 'button' => 'built'),
				   'sold' => array('title' => 'Sold', 'edit' => true, 'button' => 'sold')
);

echo "<div id=\"main\">";
renderTable2($metadata, $data);
echo "</div>";

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
