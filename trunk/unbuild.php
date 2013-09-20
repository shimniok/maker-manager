<script type='text/javascript'>actionUrl="unbuild.action.php";</script>
<?php
/**
 * unbuild.php - display build history
 * 
 * @author Michael Shimniok
 */

include_once 'common/base.php';
$pageTitle='Un-build';
include_once 'common/header.php';
include_once 'inc/data.inc.php';

$buildList = $buildhist->load();

$data = array(
	'main' => $buildList, // main table
);

$metadata = array(
	'id' => array( 'title' => 'ID', 'edit' => false ), 
	'products_id' => array( 'title' => 'Product', 'edit' => false), 
	'qty' => array( 'title' => 'Qty', 'edit' => false )
);

echo "<div id=\"main\">";
renderTable2($metadata, $data);
echo "</div>";

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
