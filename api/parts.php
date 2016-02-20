<?php
/**
 * REST interface
 *
 * Expects to be called with the following pattern:
 * GET BASE_URL/thingy       - Retrieves a list of thingys
 * GET BASE_URL/thingy/12    - Retrieves a specific thingy (#12)
 * POST BASE_URL/thingy      - Creates a new thingy
 * PUT BASE_URL/thingy/12    - Updates thingy #12
 * DELETE BASE_URL/thingy/12 - Deletes thingy #12
 */
include_once 'base.php';
require 'class.restful.php';

$columns = array('id', 'inventory', 'ordered', 'partNo', 'footprint',	'value', 'voltage', 'tolerance', 'types_id', 'subtypes_id');
$db = new Data($db, 'parts', 'id', $columns);
$rest = new Restful('parts', $db);
$rest->handleRequest();

?>
