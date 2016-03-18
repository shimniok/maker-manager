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
require_once 'Restful.php';

$path = '/types';
$table = 'types';
$pkey = 'id';
$columns = array( $pkey, 'name');

$rest = new Restful($path, $table, $columns, $pkey);

$rest->run();
