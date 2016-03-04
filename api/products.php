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
require_once __DIR__.'/silex/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$columns = array('id', 'inventory', 'needed', 'name');
$db = new Data($db, 'products', 'id', $columns);

$app->get('/products', function() use($db, $columns) {
  return json_encode($db->query());
});

$app->get('/products/{id}', function (Silex\Application $app, $id) use ($db, $columns) {
  return json_encode($db->get('id', $id, 1));
});

$app->run();


?>
