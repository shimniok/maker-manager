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

$columns = array('id', 'name');
$db = new Data($db, 'types', 'id', $columns);

$app->get('/types', function() use($db, $columns) {
  return json_encode($db->load());
});

$app->get('/types/{id}', function (Silex\Application $app, $id) use ($db, $columns) {
  return json_encode($db->loadRow('id', $id));
});

$app->run();

?>
