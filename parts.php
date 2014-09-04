<script type='text/javascript'>actionUrl="part.action.php";</script>
<?php
/**
 * parts.php - display list of parts, editable
 * 
 * @author Michael Shimniok
 */

include_once 'common/base.php';
$pageTitle='Parts';
include_once 'common/header.php';
include_once 'inc/data.inc.php';

$partList = $part->load();
$countList = $partcount->load();
$typeList = $type->load();
$subTypeList = $subtype->load();

$data = array(
	'main' => $partList, // main table
	'types' => $typeList, 
	'subtypes' => $subTypeList,
	'counts' => $countList
);

$metadata = array(
	'needed' => array( 'width' => '300', 'title' => 'Needed', 'edit' => false, 'table' => 'counts', 'key' => 'id', 'col' => 'total'), 
	'available' => array( 'title' => 'Available', 'edit' => false, 'table' => 'counts', 'key' => 'id', 'col' => 'available', 'negative' => true, 'low' => 10 ), 
	'ordered' => array( 'title' => 'Ordered', 'edit' => true, 'button' => 'ordered' ),
	'inventory' => array( 'title' => 'Inventory', 'edit' => true, 'button' => 'received' ), 
	'partNo' => array( 'title' => 'Part No.', 'edit' => true ), 
	'footprint' => array( 'title' => 'Footprint', 'edit' => true ),  
	'value' => array( 'title' => 'Value', 'edit' => true ),  
	'voltage' => array( 'title' => 'Voltage', 'edit' => true ),  
	'tolerance' => array( 'title' => 'Tolerance', 'edit' => true ),
	'types_id' => array( 'title' => 'Type', 'edit' => true, 'table' => 'types', 'key' => 'types_id', 'col' => 'name' ),
	'subtypes_id' => array( 'title' => 'Subtype', 'edit' => true, 'table' => 'subtypes', 'key' => 'subtypes_id', 'col' => 'name' )
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
renderTable($metadata, $data, true, 25);
echo "</div>";

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
