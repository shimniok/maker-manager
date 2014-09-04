<script type='text/javascript'>actionUrl="type.action.php";</script>
<?php
/**
 * types.php - display list of types, editable
 * 
 * @author Michael Shimniok
 */
include_once 'common/base.php';
$pageTitle='Types';
include_once 'common/header.php';
include_once 'inc/data.inc.php';

$data = array( 'main' => $type->load() );
$metadata = array( 'name' => array('title' => 'Name', 'edit' => true) );
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
renderTable2($metadata, $data, true, 25); // render the whole page
echo "</div>";

include_once "common/menu.php";
include_once "common/footer.php"; 
?>
