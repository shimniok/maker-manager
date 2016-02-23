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
require_once 'class.restful.php';
require_once __DIR__.'/silex/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$columns = array('id', 'products_id', 'parts_id');
$db = new Data($db, 'product_part', 'id', $columns);

$app->get('/bom', function() use($db, $columns) {
  return json_encode($db->load());
});

$app->get('/bom/{id}', function (Silex\Application $app, $id) use ($db, $columns) {
  return json_encode($db->loadList('products_id', $id));
});

$app->run();
?>