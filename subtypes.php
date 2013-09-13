<script type='text/javascript'>actionUrl="subtype.action.php";</script>
<?php
/**
 * types.php - display list of types, editable
 * 
 * @author Michael Shimniok
 */
include_once 'common/base.php';
$pageTitle='Subtypes';
include_once 'common/header.php';
include_once 'inc/data.inc.php';

$data = array( 'main' => $subtype->load() );
$metadata = array( 'name' => array('title' => 'Name', 'edit' => true) );

echo "<div id=\"main\">";
renderTable2($metadata, $data); // render the whole page
echo "</div>";

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
