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

?>
<body>
    <script src="js/jquery.inline.edit.js" type="text/javascript"></script>
    <div id="page-wrap">
        <div id="header">
            <h1><a href="/mmrp">MakerMRP</a></h1>
            <h2><?php echo $pageTitle ?></h2>
        </div>
<?php

echo "<div id=\"main\">";
echo "<span style='color: red; font-weight:bold;'>NOTE: this is an experimental page. Don't click anything.</span>";
renderTable2($metadata, $data, false, 25);
echo "</div>";

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
