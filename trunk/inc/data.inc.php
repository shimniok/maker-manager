<?php
/**
 * Data-related stuff; metadata, database things
 * 
 * $part - db object for parts table
 * $partcount - db object for partcount view
 * $type - db object for types table
 * $subtype - db object for subtypes table
 * $prod - db object for products table
 * $bom - db object for product_part table
 * $metadata - keys are a list of tables, values are arrays of metadata about each table's data
 */

$part = new Data($db, 'parts', 'id', array('types_id', 'id', 'inventory', 'ordered', 'partNo', 'footprint', 
	'value', 'voltage', 'tolerance', 'subtypes_id'));
$partcount = new Data($db, 'partcount', 'id', array('id', 'total', 'available'));
$type = new Data($db, 'types', 'id', array('name', 'id' ));
$subtype = new Data($db, 'subtypes', 'id', array('name', 'id'));
$prod = new Data($db, 'products', 'id', array('name', 'id', 'inventory', 'needed', 'sold'));
$bom = new Data($db, 'product_part', 'id', array('products_id', 'id', 'parts_id', 'qty', 'needed'));
$build = new Data($db, 'builds', 'id', array('products_id', 'id', 'qty'));

// TODO: write code to display junction table

?>
