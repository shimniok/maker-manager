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
renderTable2($metadata, $data, true, 25);
echo "</div>";

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
