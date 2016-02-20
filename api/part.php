<?php
/**
 * action script for ajax submit of Parts
 */
include_once 'base.php';
require 'class.restful.php';



$db = new Data($db, 'parts', 'id', array('id', 'inventory', 'ordered', 'partNo', 'footprint',	'value', 'voltage', 'tolerance', 'types_id', 'subtypes_id'));

$rest = new Restful("part");

$rest->handleRequest($db);

?>
